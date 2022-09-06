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
            'stakings24H' => $this->thetaService->getStakings24H(),
            'unstakings24H' => $this->thetaService->getUnstakings24H(),
            'tfuelFreeSupplyChartData' => $this->thetaService->getTfuelFreeSupplyChartData(),
            'unstakings' => $this->thetaService->getUnstakings(),
            'validators' => $this->thetaService->getValidators(),
            'accounts' => $this->thetaService->getAccounts(),
            'tdropStakingRewardRate' => $this->thetaService->getTdropStakingRewardRate(),
            'thetaData' => $this->thetaService->getThetaData(),
            'tfuelData' => $this->thetaService->getTfuelData()
        ]);
    }

    public function accounts() {
        $tags = request('tags');
        if (empty($tags)) {
            $tags = 'whales';
        }
        if ($tags == 'whales') {
            $trackingAccounts = DB::table('tracking_accounts')
                ->where('tracking_accounts.balance_usd', '>=', Constants::WHALE_MIN_BALANCE)
                ->orderByDesc('tracking_accounts.balance_usd')
                ->select('tracking_accounts.*')
                ->get();
        } else if (in_array($tags, ['validator', 'exchange'])) {
            $trackingAccounts = DB::table('tracking_accounts')
                ->join('accounts', 'tracking_accounts.code', '=', 'accounts.code')
                ->orWhere('accounts.tags', 'like', "%{$tags}%")
                ->orderByDesc('tracking_accounts.balance_usd')
                ->select('tracking_accounts.*')
                ->get();
        } else {
            $trackingAccounts = DB::table('tracking_accounts')
                ->join('accounts', 'tracking_accounts.code', '=', 'accounts.code')
                ->where('accounts.code', 'like', "%{$tags}%")
                ->orWhere('accounts.name', 'like', "%{$tags}%")
                ->orWhere('accounts.tags', 'like', "%{$tags}%")
                ->orderByDesc('tracking_accounts.balance_usd')
                ->select('tracking_accounts.*')
                ->get();
        }
        $accounts = $this->thetaService->getAccounts();
        return view('theta.accounts', [
            'trackingAccounts' => $trackingAccounts,
            'accounts' => $accounts,
            'tags' => $tags
        ]);
    }

    public function account($id)
    {
        $networkInfo = $this->thetaService->getNetworkInfo();
        $whales = $this->thetaService->getTrackingAccounts();
        $account = $this->onChainService->getAccountDetails($id, true);
        $usd = round($account['balance']['theta'] * $networkInfo['theta_price'] + $account['balance']['tfuel'] * $networkInfo['tfuel_price'] + $account['balance']['tdrop'] * $networkInfo['tdrop_price'], 2);
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
            'trackingAccounts' => $this->thetaService->getTrackingAccounts(),
            'coins' => $this->thetaService->getCoinList(),
            'whaleStatus' => $whaleStatus
        ]);
    }

    public function transaction($id)
    {
        $transaction = $this->onChainService->getTransactionDetails($id);
        $accounts = $this->thetaService->getAccounts();
        $trackingAccounts = $this->thetaService->getTrackingAccounts();
        $coins = $this->thetaService->getCoinList();
        return view('theta.transaction', [
            'transaction' => $transaction,
            'accounts' => $accounts,
            'trackingAccounts' => $trackingAccounts,
            'coins' => $coins
        ]);
    }

    public function nft()
    {
        return view('theta.nft', [
            'drops' => $this->thetaService->getDrops(),
            'networkInfo' => $this->thetaService->getNetworkInfo(),
            'thetaDropSalesChartData' => $this->thetaService->getThetaDropSalesChartData()
        ]);
    }

    public function thetaStakeChart()
    {
        return view('theta.theta_stake_chart', [
            'networkInfo' => $this->thetaService->getNetworkInfo(),
            'thetaData' => $this->thetaService->getThetaData()
        ]);
    }

    public function tfuelStakeChart()
    {
        return view('theta.tfuel_stake_chart', [
            'networkInfo' => $this->thetaService->getNetworkInfo(),
            'tfuelData' => $this->thetaService->getTfuelData()
        ]);
    }

    public function tfuelSupplyChart()
    {
        return view('theta.tfuel_supply_chart', [
            'networkInfo' => $this->thetaService->getNetworkInfo(),
            'tfuelData' => $this->thetaService->getTfuelData()
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
            'tfuelData' => $this->thetaService->getTfuelData()
        ]);
    }

    public function addWhale($id)
    {
        if ($this->thetaService->addTrackingAccount($id, null)) {
            $this->thetaService->cacheTrackingAccounts();
            return back()->with('message', ['success', 'This whale wallet added successfully.']);
        } else {
            return back()->with('message', ['error', 'Failed. This whale wallet doesn\'t meet our requirements.']);
        }
    }

    public function goldRatioChart()
    {
        return view('theta.gold_ratio_chart', [
            'data' => $this->thetaService->getHistoryPrices()
        ]);
    }

    public function thetaDropSalesChart()
    {
        return view('theta.theta_drop_sales_chart', [
            'networkInfo' => $this->thetaService->getNetworkInfo(),
            'thetaDropSalesChartData' => $this->thetaService->getThetaDropChartData()
        ]);
    }

    public function transactionsChart()
    {
        return view('theta.transactions_chart', [
            'chainData' => $this->thetaService->getChainData()
        ]);
    }

    public function tfuelBurntChart() {
        return view('theta.tfuel_burnt_chart', [
            'networkInfo' => $this->thetaService->getNetworkInfo(),
            'tfuelBurntChartData' => $this->thetaService->getTfuelBurntChartData()
        ]);
    }

    public function transactions() {
        $type = request('type');
        $account = request('account');
        $currency = request('currency');
        $sort = request('sort', 'latest_date');

        $accounts = $this->thetaService->getAccounts();
        $transactions = DB::table('transactions');
        $transactions->leftJoin('accounts AS accounts_1', 'transactions.from_account', '=', 'accounts_1.code');
        $transactions->leftJoin('accounts AS accounts_2', 'transactions.to_account', '=', 'accounts_2.code');
        $transactions->selectRaw('transactions.*, accounts_1.name AS from_name, accounts_2.name AS to_name, IF(accounts_1.id IS NOT NULL OR accounts_2.id IS NOT NULL, 1, 0) AS has_account');
        if (!empty($type)) {
            $transactions->where('type', $type);
        }
        if (!empty($account)) {
            if ($account == 'whales') {
                $transactions->where('usd', '>=', Constants::WHALE_MIN_BALANCE);
            } else if (in_array($account, ['exchange', 'validator'])) {
                $transactions->whereRaw('(accounts_1.tags LIKE ? OR accounts_2.tags LIKE ?)', ["%{$account}%", "%{$account}%"]);
            } else {
                $transactions->whereRaw('(accounts_1.name LIKE ? OR accounts_1.tags LIKE ? OR accounts_2.name LIKE ? OR accounts_2.tags LIKE ?)', ["%{$account}%", "%{$account}%", "%{$account}%", "%{$account}%"]);
            }
        }
        if (!empty($currency)) {
            $transactions->where('currency', $currency);
        }
        if (!empty($sort)) {
            if ($sort == 'large_value') {
                $transactions->orderByDesc('usd');
            } else if ($sort == 'latest_date') {
                $transactions->orderByDesc('date');
            }
        }
        $transactions = $transactions->simplePaginate(Constants::PAGINATION_PAGE_LIMIT)->withQueryString();
        return view('theta.transactions', [
            'transactions' => $transactions,
            'accounts' => $accounts,
            'sort' => $sort,
            'type' => $type,
            'account' => $account,
            'currency' => $currency,
        ]);
    }
}
