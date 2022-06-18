<?php
namespace App\Http\Controllers;

use App\Services\OnChainService;
use App\Services\ThetaService;

class ThetaController extends Controller
{

    private $thetaService;
    private $onChainService;

    public function __construct(ThetaService $thetaService, OnChainService $onChainService)
    {
        $this->thetaService = $thetaService;
        $this->onChainService = $onChainService;
    }

    public function home()
    {
        return view('theta.home', [
            'coins' => $this->thetaService->getCoinList(),
            'networkInfo' => $this->thetaService->getNetworkInfo(),
            'topTransactions' => $this->thetaService->getTopTransactions(),
            'tfuelSupplyChartData' => $this->thetaService->getTfuelSupplyChartData(),
            'tfuelFreeSupplyChartData' => $this->thetaService->getTfuelFreeSupplyChartData(),
            'thetaStakeChartData' => $this->thetaService->getThetaStakeChartData(),
            'tfuelStakeChartData' => $this->thetaService->getTfuelStakeChartData()
        ]);
    }

    public function account($id)
    {
        $account = $this->onChainService->getAccountDetails($id);
        $accounts = $this->thetaService->getAccounts();
        $coins = $this->thetaService->getCoinList();
        return view('theta.account', [
            'account' => $account,
            'accounts' => $accounts,
            'coins' => $coins
        ]);
    }

    public function transaction($id)
    {
        $transaction = $this->onChainService->getTransactionDetails($id);
        $accounts = $this->thetaService->getAccounts();
        $coins = $this->thetaService->getCoinList();
        return view('theta.transaction', [
            'transaction' => $transaction,
            'accounts' => $accounts,
            'coins' => $coins
        ]);
    }

    public function thetaStakeChart()
    {
        return view('theta.theta_stake_chart', [
            'networkInfo' => $this->thetaService->getNetworkInfo(),
            'thetaStakeChartData' => $this->thetaService->getThetaStakeChartData(),
        ]);
    }

    public function tfuelStakeChart()
    {
        return view('theta.tfuel_stake_chart', [
            'networkInfo' => $this->thetaService->getNetworkInfo(),
            'tfuelStakeChartData' => $this->thetaService->getTfuelStakeChartData(),
        ]);
    }

    public function tfuelFreeSupplyChart()
    {
        return view('theta.tfuel_free_supply_chart', [
            'tfuelFreeSupplyChartData' => $this->thetaService->getTfuelFreeSupplyChartData(),
        ]);
    }
}
