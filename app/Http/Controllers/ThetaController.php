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
            'thetaStakeChartData' => $this->thetaService->getThetaStakeChartData(),
            'tfuelStakeChartData' => $this->thetaService->getTfuelStakeChartData()
        ]);
    }

    public function account($id)
    {
        $account = $this->onChainService->getAccountDetails($id);
        $holders = $this->thetaService->getHolders();
        $coins = $this->thetaService->getCoinList();
        return view('theta.account', [
            'account' => $account,
            'holders' => $holders,
            'coins' => $coins
        ]);
    }

    public function transaction($id)
    {
        $transaction = $this->onChainService->getTransactionDetails($id);
        $holders = $this->thetaService->getHolders();
        $coins = $this->thetaService->getCoinList();
        return view('theta.transaction', [
            'transaction' => $transaction,
            'holders' => $holders,
            'coins' => $coins
        ]);
    }
}
