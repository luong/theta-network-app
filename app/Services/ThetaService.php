<?php

namespace App\Services;

use App\Models\DailyChain;
use App\Models\DailyCoin;
use App\Models\Holder;
use App\Models\NodeValidator;
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
            'tfuel_price' => $stats['tfuel']['price'],
            'tfuel_supply' => $stats['tfuel']['supply'],
            'tfuel_stake_nodes' => $stats['tfuel']['staked_nodes'],
            'tfuel_stake_rate' => round($stats['tfuel']['total_stakes'] / $stats['tfuel']['supply'], 4),
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
        $validators = NodeValidator::all()->keyBy('holder')->toArray();
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

}
