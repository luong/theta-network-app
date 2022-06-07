<?php

namespace App\Services;

use App\Helpers\Constants;
use App\Helpers\Helper;
use App\Models\DailyChain;
use App\Models\DailyCoin;
use App\Models\Holder;
use App\Models\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class ThetaService
{

    public function caching()
    {
        $this->cacheHolders();
        $this->cacheValidators();
        $this->cacheCoinList();
        $this->cacheTopTransactions();
        $this->cacheNetworkInfo();
        $this->cacheTfuelSupplyChartData();
        $this->cacheThetaStakeChartData();
        $this->cacheTfuelStakeChartData();
        $this->cacheCommandTrackers();
    }

    public function cacheCommandTrackers()
    {
        if (!Cache::has('command_trackers')) {
            $commandTrackers = [
                'Start' => ['command' => 'theta:start', 'last_run' => null],
                'UpdateDailyStats' => ['command' => 'theta:updateStats', 'last_run' => null],
                'MonitorStakes' => ['command' => 'theta:monitorStakes', 'last_run' => null],
                'UpdatePrices' => ['command' => 'theta:updatePrices', 'last_run' => null],
                'MonitorTransactions' => ['command' => 'theta:monitorTransactions', 'last_run' => null],
                'TweetDailyUpdates' => ['command' => 'theta:tweetDailyUpdates', 'last_run' => null]
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

    public function cacheNetworkInfo()
    {
        $lastChain = DailyChain::latest()->first();
        $onChainService = resolve(OnChainService::class);
        $stats = $onChainService->getThetaStats();
        $marketingData = $onChainService->getThetaMarketingData();
        $tvl = $onChainService->getTVL();
        $lastestTfuelCoins = DailyCoin::where('coin', 'tfuel')->latest()->take(2)->get();
        $lastestThetaCoins = DailyCoin::where('coin', 'theta')->latest()->take(2)->get();

        $thetaStakeChange24h = round($lastestThetaCoins[0]->total_stakes - $lastestThetaCoins[1]->total_stakes);
        $tfuelSupplyChange24h = round($lastestTfuelCoins[0]->supply - $lastestTfuelCoins[1]->supply);
        $tfuelStakeChange24h = round($lastestTfuelCoins[0]->total_stakes - $lastestTfuelCoins[1]->total_stakes);

        $info = [
            'validators' => $lastChain->validators,
            'edge_nodes' => $marketingData['edge_nodes'],
            'guardian_nodes' => $marketingData['guardian_nodes'],
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
            'tvl_change_24h' => $tvl['change_24h']
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
        $transactions = Cache::get('top_transactions');
        if (empty($transactions)) {
            $transactions = [];
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

    public function cacheHolders()
    {
        $holders = Holder::all()->keyBy('code')->toArray();
        Cache::put('holders', $holders);
        return $holders;
    }

    public function getHolders()
    {
        $holders = Cache::get('holders');
        if (empty($holders)) {
            $holders = $this->cacheHolders();
        }
        return $holders;
    }

    public function cacheValidators()
    {
        $validators = Validator::all()->keyBy('holder')->toArray();
        Cache::put('validators', $validators);
        return $validators;
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
        $chain = DailyChain::where('date', Carbon::today())->where('chain', 'theta')->first();
        if (!empty($chain)) {
            $chain->validators = $validators;
            $chain->save();
        }
    }

    public function addTopTransactions($transactions) {
        $topTransactions = $this->getTopTransactions();
        if (!empty($transactions)) {
            $topTransactions = $transactions + $topTransactions;
            uasort($topTransactions, function($tx1, $tx2) {
                if ($tx1['date'] < $tx2['date']) {
                    return 1;
                } else {
                    return -1;
                }
            });
        }
        if (count($topTransactions) > Constants::TOP_TRANSACTION_LIMIT) {
            $topTransactions = array_slice($topTransactions, 0, Constants::TOP_TRANSACTION_LIMIT);
        }
        Cache::put('top_transactions', $topTransactions);
    }
}
