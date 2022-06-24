<?php

namespace App\Services;

use App\Helpers\Constants;
use App\Models\Account;
use App\Models\DailyChain;
use App\Models\DailyCoin;
use App\Models\Stake;
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
        $this->cacheNetworkInfo();
        $this->cacheTfuelSupplyChartData();
        $this->cacheTfuelFreeSupplyChartData();
        $this->cacheEliteNodeChartData();
        $this->cacheThetaStakeChartData();
        $this->cacheTfuelStakeChartData();
        $this->cacheCommandTrackers();
        $this->cacheDrops();
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
                'DailyTweet' => ['command' => 'theta:dailyTweet', 'last_run' => null],
                'Blocks' => ['command' => 'theta:blocks', 'last_run' => null],
                'Drops' => ['command' => 'theta:drops', 'last_run' => null]
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

    public function cacheDrops()
    {
        $data = [];
        $drops = DB::table('drops')->whereDate('date', '>=', now()->subHours(24))->selectRaw('name, MAX(type) AS type, MAX(image) AS image, SUM(usd) AS usd, COUNT(*) AS times')->groupBy(['name'])->get()->toArray();
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

    public function cacheThetaStakeChartData()
    {
        $data = [];
        $coins = DailyCoin::where('coin', 'theta')->take(100)->get();
        foreach ($coins as $coin) {
            $data[] = ['x' => date('d-M', strtotime($coin->date)), 'y' => $coin->total_stakes];
        }
        Cache::put('theta_stake_chart_data', $data);
        return $data;
    }

    public function getThetaStakeChartData()
    {
        $data = Cache::get('theta_stake_chart_data');
        if (empty($data)) {
            $data = $this->cacheThetaStakeChartData();
        }
        return $data;
    }

    public function cacheTfuelStakeChartData()
    {
        $data = [];
        $coins = DailyCoin::where('coin', 'tfuel')->take(100)->get();
        foreach ($coins as $coin) {
            $data[] = ['x' => date('d-M', strtotime($coin->date)), 'y' => $coin->total_stakes];
        }
        Cache::put('tfuel_stake_chart_data', $data);
        return $data;
    }

    public function getTfuelStakeChartData()
    {
        $data = Cache::get('tfuel_stake_chart_data');
        if (empty($data)) {
            $data = $this->cacheTfuelStakeChartData();
        }
        return $data;
    }

    public function cacheTfuelSupplyChartData()
    {
        $data = [];
        $coins = DailyCoin::where('coin', 'tfuel')->take(100)->get();
        foreach ($coins as $coin) {
            $supply = $coin->supply;
            $data[] = ['x' => date('d-M', strtotime($coin->date)), 'y' => $supply];
        }
        Cache::put('tfuel_supply_chart_data', $data);
        return $data;
    }

    public function getTfuelSupplyChartData()
    {
        $data = Cache::get('tfuel_supply_chart_data');
        if (empty($data)) {
            $data = $this->cacheTfuelSupply();
        }
        return $data;
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

    public function cacheEliteNodeChartData()
    {
        $data = [];
        $coins = DailyCoin::where('coin', 'tfuel')->take(100)->get();
        foreach ($coins as $coin) {
            $data[] = ['x' => date('d-M', strtotime($coin->date)), 'y' => $coin->staked_nodes];
        }
        Cache::put('elite_node_chart_data', $data);
        return $data;
    }

    public function getEliteNodeChartData()
    {
        $data = Cache::get('elite_node_chart_data');
        if (empty($data)) {
            $data = $this->cacheEliteNodeChartData();
        }
        return $data;
    }

    public function cacheNetworkInfo()
    {
        $onChainService = resolve(OnChainService::class);
        $stats = $onChainService->getThetaStats();
        $nodeStats = $this->getNodeStats();
        $dropStats24H = $this->getDropStats24H();
        $tvl = $onChainService->getTVL();
        $lastestTfuelCoins = DailyCoin::where('coin', 'tfuel')->latest()->take(2)->get();
        $lastestThetaCoins = DailyCoin::where('coin', 'theta')->latest()->take(2)->get();

        $thetaStakeChange24h = round($lastestThetaCoins[0]->total_stakes - $lastestThetaCoins[1]->total_stakes);
        $tfuelSupplyChange24h = round($lastestTfuelCoins[0]->supply - $lastestTfuelCoins[1]->supply);
        $tfuelStakeChange24h = round($lastestTfuelCoins[0]->total_stakes - $lastestTfuelCoins[1]->total_stakes);

        $blocks24h = $onChainService->getBlocks24h();
        $blockHeight = $onChainService->getBlockHeight();
        $transactions24h = $onChainService->getTransactions24h();

        $info = [
            'validators' => $nodeStats['validators'],
            'elite_nodes' => $nodeStats['elites'],
            'guardian_nodes' => $nodeStats['guardians'],
            'onchain_wallets' => $stats['network']['onchain_wallets'],
            'active_wallets' => $stats['network']['active_wallets'],
            'theta_price' => $stats['theta']['price'],
            'theta_supply' => $stats['theta']['supply'],
            'theta_stake_nodes' => $stats['theta']['staked_nodes'],
            'theta_stake_rate' => round($stats['theta']['total_stakes'] / $stats['theta']['supply'], 4),
            'theta_stake_change_24h' => $thetaStakeChange24h,
            'tfuel_price' => $stats['tfuel']['price'],
            'tfuel_supply' => $stats['tfuel']['supply'],
            'tfuel_stake_nodes' => $stats['tfuel']['staked_nodes'],
            'tfuel_stake_rate' => round($stats['tfuel']['total_stakes'] / $stats['tfuel']['supply'], 4),
            'tfuel_supply_change_24h' => $tfuelSupplyChange24h,
            'tfuel_stake_change_24h' => $tfuelStakeChange24h,
            'tvl_value' => $tvl['current_value'],
            'tvl_change_24h' => $tvl['change_24h'],
            'blocks_24h' => $blocks24h,
            'block_height' => $blockHeight,
            'transactions_24h' => $transactions24h,
            'drop_24h' => $dropStats24H

        ];
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

    public function cacheTopWithdrawals()
    {
        $data = Stake::where('withdrawn', 1)->orderByDesc('usd')->get()->toArray();
        if (!empty($data)) {
            Cache::put('top_withdrawals', $data);
        }
        return $data;
    }

    public function getTopWithdrawals()
    {
        $data = Cache::get('top_withdrawals');
        if (empty($data)) {
            $data = $this->cacheTopWithdrawals();
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
        $onChainServer = resolve(OnChainService::class);
        $coins = $onChainServer->getCoinList();
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
        $data = DB::table('stakes')->selectRaw('type, COUNT(DISTINCT holder) AS nodes')->groupBy(['type'])->get()->keyBy('type');
        return [
            'validators' => $data['vcp']->nodes,
            'guardians' => $data['gcp']->nodes,
            'elites' => $data['eenp']->nodes,
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

}
