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
        $networkInfo = $this->thetaService->getNetworkInfo();
        $whales = $this->thetaService->getTrackingAccounts();
        $account = $this->onChainService->getAccountDetails($id);
        $usd = round($account['balance']['theta'] * $networkInfo['theta_price'] + $account['balance']['tfuel'] * $networkInfo['tfuel_price'], 2);
        $whaleStatus = 'no';
        if ($usd >= Constants::WHALE_MIN_BALANCE) {
            if (isset($whales[$id])) {
                $whaleStatus = 'identified';
            } else {
                $whaleStatus = 'not_identified';
            }
        } else {
            if (isset($whales[$id])) {
                $trackingAccount = TrackingAccount::where('code', $id)->first();
                if (!empty($trackingAccount)) {
                    $trackingAccount->delete();
                }
            }
        }
        return view('theta.account', [
            'account' => $account,
            'accounts' => $this->thetaService->getAccounts(),
            'coins' => $this->thetaService->getCoinList(),
            'whaleStatus' => $whaleStatus
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

    public function addWhale($id)
    {
        if ($this->thetaService->addWhaleAccount($id, null)) {
            $this->thetaService->cacheTrackingAccounts();
            return back()->with('message', ['success', 'This whale wallet added successfully.']);
        } else {
            return back()->with('message', ['error', 'Failed. This whale wallet doesn\'t meet our requirements.']);
        }
    }
}
