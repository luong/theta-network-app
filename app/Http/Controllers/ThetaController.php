<?php
namespace App\Http\Controllers;

use App\Helpers\Constants;
use App\Models\TrackingAccount;
use App\Services\OnChainService;
use App\Services\ThetaService;
use Illuminate\Support\Facades\DB;

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
            'eliteNodeChartData' => $this->thetaService->getEliteNodeChartData(),
            'thetaStakeChartData' => $this->thetaService->getThetaStakeChartData(),
            'tfuelStakeChartData' => $this->thetaService->getTfuelStakeChartData(),
            'topWithdrawals' => $this->thetaService->getTopWithdrawals(),
            'validators' => $this->thetaService->getValidators(),
            'accounts' => $this->thetaService->getAccounts()
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

    public function nft()
    {
        return view('theta.nft', [
            'drops' => $this->thetaService->getDrops(),
            'networkInfo' => $this->thetaService->getNetworkInfo()
        ]);
    }

    public function thetaStakeChart()
    {
        return view('theta.theta_stake_chart', [
            'networkInfo' => $this->thetaService->getNetworkInfo(),
            'thetaStakeChartData' => $this->thetaService->getThetaStakeChartData()
        ]);
    }

    public function tfuelStakeChart()
    {
        return view('theta.tfuel_stake_chart', [
            'networkInfo' => $this->thetaService->getNetworkInfo(),
            'tfuelStakeChartData' => $this->thetaService->getTfuelStakeChartData()
        ]);
    }

    public function tfuelFreeSupplyChart()
    {
        return view('theta.tfuel_free_supply_chart', [
            'tfuelFreeSupplyChartData' => $this->thetaService->getTfuelFreeSupplyChartData()
        ]);
    }

    public function eliteNodeChart()
    {
        return view('theta.elite_node_chart', [
            'eliteNodeChartData' => $this->thetaService->getEliteNodeChartData()
        ]);
    }

    public function whales()
    {
        return view('theta.whales', [
            'accounts' => $this->thetaService->getAccounts(),
            'trackingAccounts' => $this->thetaService->getTrackingAccounts()
        ]);
    }

    public function addWhale()
    {
        $address = request('address');
        $name = request('name');
        $acc = $this->onChainService->getAccount($address);
        if ($acc !== false) {
            $networkInfo = $this->thetaService->getNetworkInfo();
            $usd = round($acc['balance']['theta'] * $networkInfo['theta_price'] + $acc['balance']['tfuel'] * $networkInfo['tfuel_price'], 2);
            if ($usd >= Constants::WHALE_MIN_BALANCE) {
                TrackingAccount::updateOrCreate(
                    ['code' => $address],
                    [
                        'code' => $address,
                        'name' => $name,
                        'balance_theta' => round($acc['balance']['theta'], 2),
                        'balance_tfuel' => round($acc['balance']['tfuel'], 2),
                        'balance_usd' => $usd
                    ]
                );
                $this->thetaService->cacheTrackingAccounts();
                return back()->with('message', ['success', 'This whale wallet added successfully.']);
            }
        }
        return back()->with('message', ['error', 'Failed. This whale wallet doesn\'t meet our requirements.']);
    }
}
