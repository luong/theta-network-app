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
    }

    public function cacheTfuelSupplyChartData() {
        $data = [];
        $coins = DailyCoin::where('coin', 'tfuel')->take(100)->get();
        foreach ($coins as $coin) {
            $supply = $coin->supply;
            $data[] = ['x' => date('d-M', strtotime($coin->date)), 'y' => $supply];
        }
        Cache::put('tfuel_supply_chart_data', $data);
        return $data;
    }

    public function getTfuelSupplyChartData() {
        $data = Cache::get('tfuel_supply_chart_data');
        if (empty($data)) {
            $data = $this->cacheTfuelSupply();
        }
        return $data;
    }

    public function cacheNetworkInfo()
    {
        $lastChain = DailyChain::latest()->first();
        $lastThetaCoin = DailyCoin::where('coin', 'theta')->latest()->first();
        $lastTfuelCoin = DailyCoin::where('coin', 'tfuel')->latest()->first();
        $info = [
            'validators' => $lastChain->validators,
            'edge_nodes' => $lastChain->metadata['edge_nodes'],
            'guardian_nodes' => $lastChain->metadata['guardian_nodes'],
            'onchain_wallets' => $lastChain->onchain_wallets,
            'active_wallets' => $lastChain->active_wallets,
            'theta_stake_nodes' => $lastThetaCoin->staked_nodes,
            'theta_stake_rate' => round($lastThetaCoin->total_stakes / $lastThetaCoin->supply, 4),
            'tfuel_stake_nodes' => $lastTfuelCoin->staked_nodes,
            'tfuel_stake_rate' => round($lastTfuelCoin->total_stakes / $lastTfuelCoin->supply, 4),
        ];
        Cache::put('network_info', $info);
        return $info;
    }

    public function getNetworkInfo() {
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
