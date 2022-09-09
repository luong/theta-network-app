<?php

namespace App\Services;

use App\Helpers\Constants;
use App\Models\Account;
use App\Models\DailyChain;
use App\Models\DailyCoin;
use App\Models\Stake;
use App\Models\TrackingAccount;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ThetaService
{

    public function caching()
    {
        $this->cacheAccounts();
        $this->cacheValidators();
        $this->cacheCoinList();
        $this->cacheTopTransactions();
        $this->cacheStakings24H();
        $this->cacheUnstakings24H();
        $this->cacheNetworkInfo();
        $this->cacheCommandTrackers();
        $this->cacheDrops();
        $this->cacheSettings();
        $this->cacheHistoryPrices();
        $this->cacheChainData();
        $this->cacheThetaData();
        $this->cacheTfuelData();
        $this->cacheTdropData();
    }

    public function recaching()
    {
        Cache::forget('command_trackers');
        $this->caching();
    }

    public function cacheCommandTrackers()
    {
        if (!Cache::has('command_trackers')) {
            $commandTrackers = [
                'Start' => ['command' => 'theta:start', 'last_run' => null],
                'DailyStats' => ['command' => 'theta:dailyStats', 'last_run' => null],
                'Stakes' => ['command' => 'theta:stakes', 'last_run' => null],
                'Prices' => ['command' => 'theta:prices', 'last_run' => null],
                'Transactions' => ['command' => 'theta:transactions', 'last_run' => null],
                'TdropTransactions' => ['command' => 'theta:tdropTransactions', 'last_run' => null],
                'DailyTweet2' => ['command' => 'theta:dailyTweet2', 'last_run' => null],
                'Drops' => ['command' => 'theta:drops', 'last_run' => null],
                'Accounts' => ['command' => 'theta:accounts', 'last_run' => null],
                'News' => ['command' => 'theta:news', 'last_run' => null],
                'BinanceListing' => ['command' => 'theta:binanceListing', 'last_run' => null]
            ];
            Cache::put('command_trackers', $commandTrackers);
        }
        return Cache::get('command_trackers');
    }

    public function getCommandTrackers()
    {
        $commandTrackers = Cache::get('command_trackers');
        if (empty($commandTrackers)) {
            $commandTrackers = $this->cacheCommandTrackers();
        }
        return $commandTrackers;
    }

    public function setCommandTracker($command, $property, $value)
    {
        $commands = $this->getCommandTrackers();
        $commands[$command][$property] = $value;
        Cache::put('command_trackers', $commands);

        if (in_array($command, ['Start', 'TweetDailyUpdates'])) {
            Log::channel('db')->info("Command {$command} started");
        }
    }

    public function getChainData()
    {
        $data = Cache::get('chain_data');
        if (empty($data)) {
            $data = $this->cacheChainData();
        }
        return $data;
    }

    public function cacheChainData()
    {
        $data = DailyChain::all()->toArray();
        Cache::put('chain_data', $data);
        return $data;
    }

    public function getThetaData()
    {
        $data = Cache::get('theta_data');
        if (empty($data)) {
            $data = $this->cacheThetaData();
        }
        return $data;
    }

    public function cacheThetaData()
    {
        $data = DailyCoin::where('coin', 'theta')->get()->toArray();
        Cache::put('theta_data', $data);
        return $data;
    }

    public function getTfuelData()
    {
        $data = Cache::get('tfuel_data');
        if (empty($data)) {
            $data = $this->cacheTfuelData();
        }
        return $data;
    }

    public function cacheTfuelData()
    {
        $data = DailyCoin::where('coin', 'tfuel')->get()->toArray();
        Cache::put('tfuel_data', $data);
        return $data;
    }

    public function getTdropData()
    {
        $data = Cache::get('tdrop_data');
        if (empty($data)) {
            $data = $this->cacheTdropData();
        }
        return $data;
    }

    public function cacheTdropData()
    {
        $data = DailyCoin::where('coin', 'tdrop')->get()->toArray();
        Cache::put('tdrop_data', $data);
        return $data;
    }

    public function cacheDrops()
    {
        $data = [];
        $drops = DB::table('drops')->whereDate('date', '>=', now()->subHours(24))->selectRaw('MAX(transaction_id) AS transaction_id, name, MAX(type) AS type, MAX(image) AS image, SUM(usd) AS usd, COUNT(*) AS times')->groupBy(['name'])->get()->toArray();
        $totalUsd = 0;
        foreach ($drops as $drop) {
            $totalUsd += $drop->usd;
        }
        $data = [];
        foreach ($drops as $drop) {
            $class = 'drop';
            $percent = ($drop->usd / $totalUsd) * 100;
            if ($percent >= 3) {
                $class = 'drop drop3';
            } else if ($percent >= 1.5) {
                $class = 'drop drop2';
            }
            $data[] = [
                'transaction_id' => $drop->transaction_id,
                'name' => $drop->name,
                'image' => $drop->image,
                'class' => $class,
                'type' => $drop->type
            ];
        }
        Cache::put('drops', $data);
        return $data;
    }

    public function getDrops()
    {
        $data = Cache::get('drops');
        if (empty($data)) {
            $data = $this->cacheDrops();
        }
        return $data;
    }

    public function getTdropStakingRewardRate()
    {
        $data = Cache::get('tdropStakingRewardRate');
        if (empty($data)) {
            $data = $this->cacheTdropStakingRewardRate();
        }
        return $data;
    }

    public function cacheTdropStakingRewardRate() {
        $tdropContract = resolve(TdropContract::class);
        $rate = $tdropContract->stakesRewardAnnualRate();
        Cache::put('tdropStakingRewardRate', $rate, now()->addMinutes(30));
        return $rate;
    }

    public function getThetaDropChartData()
    {
        $result = [];
        $data = $this->getChainData();
        foreach ($data as $each) {
            if (empty($each['drops'])) {
                continue;
            }
            $result[] = ['date' => $each['date'], 'drop' => $each['drops']];
        }
        return $result;
    }

    public function getTfuelBurntChartData()
    {
        $result = [];
        $data = $this->getTfuelData();
        foreach ($data as $i => $each) {
            if (empty($each['total_burnt'])) {
                continue;
            }
            if (empty($data[$i-1]) || empty($data[$i-1]['total_burnt'])) {
                continue;
            }
            $result[] = ['x' => $each['date'], 'y' => round($each['total_burnt'] - $data[$i-1]['total_burnt'], 0)];
        }
        return $result;
    }


    public function cacheTfuelFreeSupplyChartData()
    {
        $data = [];
        $coins = DailyCoin::where('coin', 'tfuel')->take(100)->get();
        foreach ($coins as $coin) {
            $supply = $coin->supply - $coin->total_stakes;
            $data[] = ['x' => date('d-M', strtotime($coin->date)), 'y' => $supply];
        }
        Cache::put('tfuel_free_supply_chart_data', $data);
        return $data;
    }

    public function getTfuelFreeSupplyChartData()
    {
        $data = Cache::get('tfuel_free_supply_chart_data');
        if (empty($data)) {
            $data = $this->cacheTfuelFreeSupplyChartData();
        }
        return $data;
    }

    public function cacheNetworkInfo()
    {
        $onChainService = resolve(OnChainService::class);
        $stats = $onChainService->getThetaStats();
        $coinList = $this->getCoinList();
        $nodeStats = $this->getNodeStats();
        $dropStats24H = $this->getDropStats24H();
        $tvl = $onChainService->getTVL();
        $lastestChains = DailyChain::latest()->take(2)->get();
        $lastestTfuelCoins = DailyCoin::where('coin', 'tfuel')->latest()->take(2)->get();
        $lastestThetaCoins = DailyCoin::where('coin', 'theta')->latest()->take(2)->get();
        $lastestTdropCoins = DailyCoin::where('coin', 'tdrop')->latest()->take(2)->get();

        $thetaStakeChange24h = round($lastestThetaCoins[0]->total_stakes - $lastestThetaCoins[1]->total_stakes);
        $tfuelSupplyChange24h = $lastestTfuelCoins[1]->supply == 0 ? 0 : round($lastestTfuelCoins[0]->supply - $lastestTfuelCoins[1]->supply);
        $tfuelTotalSupplyChange24h = $lastestTfuelCoins[1]->total_supply == 0 ? 0 : round($lastestTfuelCoins[0]->total_supply - $lastestTfuelCoins[1]->total_supply);
        $tfuelTotalBurntChange24h = $lastestTfuelCoins[1]->total_burnt == 0 ? 0 : round($lastestTfuelCoins[0]->total_burnt - $lastestTfuelCoins[1]->total_burnt);
        $tfuelStakeChange24h = round($lastestTfuelCoins[0]->total_stakes - $lastestTfuelCoins[1]->total_stakes);
        $guardianNodesChange24h = round($lastestChains[0]->nodes['guardians'] - $lastestChains[1]->nodes['guardians']);
        $eliteNodesChange24h = round($lastestChains[0]->nodes['elites'] - $lastestChains[1]->nodes['elites']);
        $activeWalletsChange24h = round($lastestChains[0]->active_wallets - $lastestChains[1]->active_wallets);

        $dropTimes = $lastestChains[0]->drops['times'];
        $dropSales = $lastestChains[0]->drops['total'];
        $dropTimesChange24h = round(($lastestChains[0]->drops['times'] - $lastestChains[1]->drops['times']) / $lastestChains[1]->drops['times'], 2);
        $dropSalesChange24h = @round(($lastestChains[0]->drops['total'] - $lastestChains[1]->drops['total']) / $lastestChains[1]->drops['total'], 2);

        $thetaVolChange24h = $lastestThetaCoins[0]->volume_24h - $lastestThetaCoins[1]->volume_24h;
        $tfuelVolChange24h = $lastestTfuelCoins[0]->volume_24h - $lastestTfuelCoins[1]->volume_24h;
        $tdropVolChange24h = $lastestTdropCoins[0]->volume_24h - $lastestTdropCoins[1]->volume_24h;
        $tdropSupplyChange24h = round($lastestTdropCoins[0]->supply - $lastestTdropCoins[1]->supply);
        $tdropStakeChange24h = round($lastestTdropCoins[0]->total_stakes - $lastestTdropCoins[1]->total_stakes);

        $blocks24h = $onChainService->getBlocks24h();
        $blockHeight = $onChainService->getBlockHeight();
        $transactions24h = $onChainService->getTransactions24h();

        $tdropContract = resolve(TdropContract::class);
        $tdropRewardRate = $tdropContract->stakesRewardAnnualRate();

        $info = [
            'validators' => $nodeStats['validators'],
            'elite_nodes' => $nodeStats['elites'],
            'elite_nodes_change_24h' => $eliteNodesChange24h,
            'guardian_nodes' => $nodeStats['guardians'],
            'guardian_nodes_change_24h' => $guardianNodesChange24h,
            'onchain_wallets' => !empty($stats['network']['onchain_wallets']) ? $stats['network']['onchain_wallets'] : 0,
            'active_wallets' => !empty($stats['network']['active_wallets']) ? $stats['network']['active_wallets'] : 0,
            'active_wallets_change_24h' => $activeWalletsChange24h,
            'theta_price' => $stats['theta']['price'],
            'theta_supply' => $stats['theta']['supply'],
            'theta_stake_nodes' => $nodeStats['guardians'] + $nodeStats['validators'],
            'theta_stake_rate' => round($stats['theta']['total_stakes'] / $stats['theta']['supply'], 4),
            'theta_stake_change_24h' => $thetaStakeChange24h,
            'theta_volume_change_24h' => $thetaVolChange24h,
            'tfuel_price' => $stats['tfuel']['price'],
            'tfuel_supply' => $stats['tfuel']['supply'],
            'tfuel_stake_nodes' => $nodeStats['elites'],
            'tfuel_stake_rate' => round($stats['tfuel']['total_stakes'] / $stats['tfuel']['supply'], 4),
            'tfuel_supply_change_24h' => $tfuelSupplyChange24h,
            'tfuel_total_supply_change_24h' => $tfuelTotalSupplyChange24h,
            'tfuel_total_burnt_change_24h' => $tfuelTotalBurntChange24h,
            'tfuel_stake_change_24h' => $tfuelStakeChange24h,
            'tfuel_volume_change_24h' => $tfuelVolChange24h,
            'tdrop_price' => $coinList['TDROP']['price'],
            'tdrop_volume_change_24h' => $tdropVolChange24h,
            'tdrop_supply' => $stats['tdrop']['supply'],
            'tdrop_supply_change_24h' => $tdropSupplyChange24h,
            'tdrop_stake_change_24h' => $tdropStakeChange24h,
            'tdrop_stake_rate' => round($stats['tdrop']['total_stakes'] / $stats['tdrop']['supply'], 4),
            'tdrop_reward_rate' => $tdropRewardRate,
            'tvl_value' => $tvl['current_value'],
            'tvl_change_24h' => $tvl['change_24h'],
            'drop_24h' => $dropStats24H,
            'drop_times' => $dropTimes,
            'drop_sales' => $dropSales,
            'drop_times_change_24h' => $dropTimesChange24h,
            'drop_sales_change_24h' => $dropSalesChange24h

        ];
        if ($blocks24h > 0) {
            $info['blocks_24h'] = $blocks24h;
        }
        if ($blockHeight > 0) {
            $info['block_height'] = $blockHeight;
        }
        if ($transactions24h > 0) {
            $info['transactions_24h'] = $transactions24h;
        }

        Cache::put('network_info', $info);
        return $info;
    }

    public function getNetworkInfo()
    {
        $info = Cache::get('network_info');
        if (empty($info)) {
            $info = $this->cacheNetworkInfo();
        }
        return $info;
    }

    public function cacheTopTransactions()
    {
        $transactions = Transaction::whereDate('date', '>=', now()->subHours(24))->orderByDesc('usd')->take(Constants::TOP_TRANSACTION_LIMIT)->get()->toArray();
        if (!empty($transactions)) {
            Cache::put('top_transactions', $transactions);
        }
        return $transactions;
    }

    public function getTopTransactions()
    {
        $transactions = Cache::get('top_transactions');
        if (empty($transactions)) {
            $transactions = $this->cacheTopTransactions();
        }
        return $transactions;
    }

    public function cacheUnstakings()
    {
        $data = [
            'theta' => 0,
            'tfuel' => 0,
            'list' => []
        ];
        $result = Stake::where('withdrawn', 1)->orderByDesc('usd')->get()->toArray();
        if (!empty($result)) {
            $data['list'] = $result;
            foreach ($result as $each) {
                if ($each['currency'] == 'theta') {
                    $data['theta'] += $each['coins'];
                } else if ($each['currency'] == 'tfuel') {
                    $data['tfuel'] += $each['coins'];
                }
            }
            Cache::put('unstakings', $data);
        }
        return $data;
    }

    public function getUnstakings()
    {
        $data = Cache::get('unstakings');
        if (empty($data)) {
            $data = $this->cacheUnstakings();
        }
        return $data;
    }

    public function cacheStakings24H()
    {
        $data = [
            'theta' => 0,
            'tfuel' => 0,
            'list' => []
        ];
        $result = Transaction::where('type', 'stake')->whereIn('currency', ['theta', 'tfuel'])->whereDate('date', '>=', now()->subHours(24))->orderByDesc('usd')->get()->toArray();
        if (!empty($result)) {
            $data['list'] = $result;
            foreach ($result as $each) {
                if ($each['currency'] == 'theta') {
                    $data['theta'] += $each['coins'];
                } else if ($each['currency'] == 'tfuel') {
                    $data['tfuel'] += $each['coins'];
                }
            }
            Cache::put('stakings24H', $data);
        }
        return $data;
    }

    public function getStakings24H()
    {
        $data = Cache::get('stakings24H');
        if (empty($data)) {
            $data = $this->cacheStakings24H();
        }
        return $data;
    }

    public function cacheUnstakings24H()
    {
        $data = [
            'theta' => 0,
            'tfuel' => 0,
            'list' => []
        ];
        $result = Transaction::where('type', 'unstake')->whereIn('currency', ['theta', 'tfuel'])->whereDate('date', '>=', now()->subHours(24))->orderByDesc('usd')->get()->toArray();
        if (!empty($result)) {
            $data['list'] = $result;
            foreach ($result as $each) {
                if ($each['currency'] == 'theta') {
                    $data['theta'] += $each['coins'];
                } else if ($each['currency'] == 'tfuel') {
                    $data['tfuel'] += $each['coins'];
                }
            }
            Cache::put('unstakings24H', $data);
        }
        return $data;
    }

    public function getUnstakings24H()
    {
        $data = Cache::get('unstakings24H');
        if (empty($data)) {
            $data = $this->cacheUnstakings24H();
        }
        return $data;
    }

    public function cacheAccounts()
    {
        $accounts = Account::all()->keyBy('code')->toArray();
        Cache::put('accounts', $accounts);
        return $accounts;
    }

    public function getAccounts()
    {
        $accounts = Cache::get('accounts');
        if (empty($accounts)) {
            $accounts = $this->cacheAccounts();
        }
        return $accounts;
    }

    public function cacheValidators()
    {
        $stakes = Stake::where('type', '=', 'vcp')->get();
        $holders = [];
        foreach ($stakes as $stake) {
            if (!isset($holders[$stake->holder])) {
                $holders[$stake->holder] = ['coins' => 0, 'stakers' => []];
            }
            $holders[$stake->holder]['coins'] += $stake->coins;
            $holders[$stake->holder]['stakers'][] = $stake->toArray();
        }
        uasort($holders, function($a, $b) {
            if ($a['coins'] < $b['coins']) {
                return 1;
            } else {
                return -1;
            }
        });
        Cache::put('validators', $holders);
        return $holders;
    }

    public function getValidators()
    {
        $validators = Cache::get('validators');
        if (empty($validators)) {
            $validators = $this->cacheValidators();
        }
        return $validators;
    }

    public function cacheCoinList()
    {
        $onChainService = resolve(OnChainService::class);
        $coins = $onChainService->getCoinList();
        Cache::put('coins', $coins);
        return $coins;
    }

    public function getCoinList()
    {
        $coins = Cache::get('coins');
        if (empty($coins)) {
            $coins = $this->cacheCoinList();
        }
        return $coins;
    }

    public function updateDailyValidators(int $validators)
    {
        $chain = DailyChain::where('date', Carbon::today())->first();
        if (!empty($chain)) {
            $chain->validators = $validators;
            $chain->save();
        }
    }

    public function getNodeStats()
    {
        $data = DB::table('stakes')->where('withdrawn', 0)->selectRaw('type, COUNT(DISTINCT holder) AS nodes')->groupBy(['type'])->get()->keyBy('type');
        return [
            'validators' => isset($data['vcp']) ? $data['vcp']->nodes : 0,
            'guardians' => isset($data['gcp']) ? $data['gcp']->nodes : 0,
            'elites' => isset($data['eenp']) ? $data['eenp']->nodes : 0,
        ];
    }

    public function getDropStats24H()
    {
        $data = DB::table('drops')->whereDate('date', '>=', now()->subHours(24))->selectRaw("SUM(IF(currency = 'stable_coin', usd, 0)) AS total_usd, SUM(IF(currency = 'tfuel', tfuel, 0)) AS total_tfuel, SUM(usd) AS total, SUM(IF(currency = 'stable_coin', 1, 0)) AS times_usd, SUM(IF(currency = 'tfuel', 1, 0)) AS times_tfuel, COUNT(*) AS times")->get();
        return [
            'times_usd' => $data[0]->times_usd,
            'times_tfuel' => $data[0]->times_tfuel,
            'times' => $data[0]->times,
            'total_usd' => round($data[0]->total_usd, 2),
            'total_tfuel' => round($data[0]->total_tfuel, 2),
            'total' => round($data[0]->total, 2)
        ];
    }

    public function addTrackingAccount($accountId, $name = null, $networkInfo = null, $useTdrop = false)
    {
        $onChainService = resolve(OnChainService::class);
        $acc = $onChainService->getAccount($accountId, $useTdrop);
        if ($acc !== false) {
            if (empty($networkInfo)) {
                $networkInfo = $this->getNetworkInfo();
            }
            $usd = round($acc['balance']['theta'] * $networkInfo['theta_price'] + $acc['balance']['tfuel'] * $networkInfo['tfuel_price'] + $acc['balance']['tdrop'] * $networkInfo['tdrop_price'], 2);
            TrackingAccount::updateOrCreate(
                ['code' => $accountId],
                [
                    'code' => $accountId,
                    'name' => $name,
                    'balance_theta' => round($acc['balance']['theta'], 2),
                    'balance_tfuel' => round($acc['balance']['tfuel'], 2),
                    'balance_tdrop' => round($acc['balance']['tdrop'], 2),
                    'staking_theta' => round($acc['staking']['theta'], 2),
                    'staking_tfuel' => round($acc['staking']['tfuel'], 2),
                    'staking_tdrop' => round($acc['staking']['tdrop'], 2),
                    'balance_usd' => $usd
                ]
            );
            return true;
        }
        return false;
    }

    public function cacheTrackingAccounts()
    {
        $trackingAccounts = DB::table('tracking_accounts')->where('balance_usd', '>=', Constants::WHALE_MIN_BALANCE)->orderByDesc('balance_usd')->get()->toArray();
        $data = [];
        foreach ($trackingAccounts as $trackingAccount) {
            $data[$trackingAccount->code] = (array)$trackingAccount;
        }
        Cache::put('trackingAccounts', $data);
        return $data;
    }

    public function getTrackingAccounts()
    {
        $data = Cache::get('trackingAccounts');
        if (empty($data)) {
            $data = $this->cacheTrackingAccounts();
        }
        return $data;
    }

    public function cacheSettings()
    {
        $settings = DB::table('settings')->get();
        $data = [];
        foreach ($settings as $setting) {
            $data[$setting->code] = $setting->value;
        }
        Cache::put('settings', $data);
        return $data;
    }

    public function getSettings()
    {
        $data = Cache::get('settings');
        if (empty($data)) {
            $data = $this->cacheSettings();
        }
        return $data;
    }

    public function getStakingsByAccountId($accountId) {
        $result = DB::select("SELECT source, currency, SUM(coins) AS coins FROM stakes GROUP BY source, currency HAVING source = ?", [$accountId]);
        $theta = 0;
        $tfuel = 0;
        if (!empty($result)) {
            foreach ($result as $each) {
                if ($each->currency == 'theta') {
                    $theta = $each->coins;
                } else if ($each->currency == 'tfuel') {
                    $tfuel = $each->coins;
                }
            }
        }
        return ['theta' => $theta, 'tfuel' => $tfuel, 'tdrop' => 0];
    }

    public function cacheWallets() {
        $wallets = DB::select("SELECT wallets.address, users.email FROM wallets INNER JOIN users ON wallets.user_id = users.id");
        $result = [];
        if (!empty($wallets)) {
            foreach ($wallets as $each) {
                $result[$each->address] = $each->email;
            }
        }
        Cache::put('wallets', $result);
        return $result;
    }

    public function getWallets()
    {
        $data = Cache::get('wallets');
        if (empty($data)) {
            $data = $this->cacheWallets();
        }
        return $data;
    }

    public function getHistoryPrices()
    {
        $data = Cache::get('history_prices');
        if (empty($data)) {
            $data = $this->cacheHistoryPrices();
        }
        return $data;
    }

    public function cacheHistoryPrices()
    {
        $onChainService = resolve(OnChainService::class);
        $data = $onChainService->getHistoryPricesInBinance();
        Cache::put('history_prices', $data);
        return $data;
    }

}
