<?php
namespace App\Http\Controllers;

use App\Helpers\Constants;
use App\Models\TrackingAccount;
use App\Models\Transaction;
use App\Services\OnChainService;
use App\Services\TdropContract;
use App\Services\ThetaService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

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
        } else if (in_array($tags, ['validator'])) {
            $trackingAccounts = DB::table('tracking_accounts')
                ->join('accounts', 'tracking_accounts.code', '=', 'accounts.code')
                ->orWhere('accounts.tags', 'like', "%validator_member%")
                ->orderByDesc('tracking_accounts.balance_usd')
                ->select('tracking_accounts.*')
                ->get();
        } else if (in_array($tags, ['exchange'])) {
            $trackingAccounts = DB::table('tracking_accounts')
                ->join('accounts', 'tracking_accounts.code', '=', 'accounts.code')
                ->orWhere('accounts.tags', 'like', "%exchange%")
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
        $account = $this->onChainService->getAccount($id, true);
        $account['stakes'] = $this->onChainService->getAccountStakes($id);

        $tdropStakes = resolve(TdropContract::class)->getStakingEstimatedTDropOwnedBy($id);
        if ($tdropStakes > 0) {
            $account['stakes'][] = [
                'id' => '',
                'role' => '',
                'type' => 'tdrop',
                'holder' => Constants::TDROP_STAKING_ADDRESS,
                'source' => $id,
                'coins' => $tdropStakes,
                'currency' => 'tdrop',
                'status' => 'staking',
                'return_height' => 0
            ];
        }

        $transactions = DB::table('transactions')
            ->where('from_account', $id)
            ->orWhere('to_account', $id)
            ->orderByDesc('date')
            ->simplePaginate(Constants::PAGINATION_PAGE_LIMIT)
            ->withQueryString();
        return view('theta.account', [
            'account' => $account,
            'accounts' => $this->thetaService->getAccounts(),
            'trackingAccounts' => $this->thetaService->getTrackingAccounts(),
            'coins' => $this->thetaService->getCoinList(),
            'transactions' => $transactions
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
        $days = request('days', '30D');
        $sort = request('sort', 'latest_date');

        $accounts = $this->thetaService->getAccounts();
        $transactions = DB::table('transactions');
        $transactions->leftJoin('accounts AS accounts_1', 'transactions.from_account', '=', 'accounts_1.code');
        $transactions->leftJoin('accounts AS accounts_2', 'transactions.to_account', '=', 'accounts_2.code');
        $transactions->leftJoin('tracking_accounts AS tracking_accounts_1', 'transactions.from_account', '=', 'tracking_accounts_1.code');
        $transactions->leftJoin('tracking_accounts AS tracking_accounts_2', 'transactions.to_account', '=', 'tracking_accounts_2.code');
        $transactions->selectRaw('transactions.*, accounts_1.name AS from_name, accounts_2.name AS to_name, IF(accounts_1.id IS NOT NULL OR accounts_2.id IS NOT NULL, 1, 0) AS has_account');
        if (!empty($type)) {
            $transactions->where('type', $type);
        }
        if (!empty($account)) {
            if ($account == 'whales') {
                $transactions->whereRaw('((tracking_accounts_1.id IS NOT NULL AND tracking_accounts_1.balance_usd >= ?) OR (tracking_accounts_2.id IS NOT NULL AND tracking_accounts_2.balance_usd >= ?))', [Constants::WHALE_MIN_BALANCE, Constants::WHALE_MIN_BALANCE]);
            } else if (in_array($account, ['exchange', 'validator'])) {
                $transactions->whereRaw('(accounts_1.tags LIKE ? OR accounts_2.tags LIKE ?)', ["%{$account}%", "%{$account}%"]);
            } else {
                $transactions->whereRaw('(accounts_1.name LIKE ? OR accounts_1.tags LIKE ? OR accounts_2.name LIKE ? OR accounts_2.tags LIKE ?)', ["%{$account}%", "%{$account}%", "%{$account}%", "%{$account}%"]);
            }
        }
        if (!empty($currency)) {
            $transactions->where('currency', $currency);
        }

        if ($days == '1D') {
            $transactions->whereDate('date', '>=', date('Y-m-d H:i:s', strtotime('-24 hours')));
        } else if ($days == '3D') {
            $transactions->whereDate('date', '>=' , date('Y-m-d H:i:s', strtotime('-3 days')));
        } else if ($days == '7D') {
            $transactions->whereDate('date', '>=' , date('Y-m-d H:i:s', strtotime('-7 days')));
        } else if ($days == '30D') {
            $transactions->whereDate('date', '>=' , date('Y-m-d H:i:s', strtotime('-30 days')));
        }

        if ($sort == 'large_value') {
            $transactions->orderByDesc('usd');
        } else if ($sort == 'latest_date') {
            $transactions->orderByDesc('date');
        }
        $transactions = $transactions->simplePaginate(Constants::PAGINATION_PAGE_LIMIT)->withQueryString();
        return view('theta.transactions', [
            'transactions' => $transactions,
            'accounts' => $accounts,
            'days' => $days,
            'sort' => $sort,
            'type' => $type,
            'account' => $account,
            'currency' => $currency,
        ]);
    }

    public function volumes() {
        $days = request('days', '30D');
        $sort = request('sort', 'volume_in');
        $currency = request('currency');

        $accounts = $this->thetaService->getAccounts();

        $query1 = DB::table('transactions')->selectRaw('currency, from_account as account, 0 as in_theta_coins, 0 as in_tfuel_coins, 0 as in_tdrop_coins, IF(currency = "theta", coins, 0) as out_theta_coins, IF(currency = "tfuel", coins, 0) as out_tfuel_coins, IF(currency = "tdrop", coins, 0) as out_tdrop_coins, 0 as usd_in, usd as usd_out, date')
            ->union(DB::table('transactions')->selectRaw('currency, to_account as account, IF(currency = "theta", coins, 0) as in_theta_coins, IF(currency = "tfuel", coins, 0) as in_tfuel_coins, IF(currency = "tdrop", coins, 0) as in_tdrop_coins, 0 as out_theta_coins, 0 as out_tfuel_coins, 0 as out_tdrop_coins, usd as usd_in, 0 as usd_out, date'));

        $query2 = Transaction::query()->fromSub($query1, 't1')
            ->selectRaw('account, count(*) as times, sum(in_theta_coins) as in_theta_coins, sum(in_tfuel_coins) as in_tfuel_coins, sum(in_tdrop_coins) as in_tdrop_coins, sum(out_theta_coins) as out_theta_coins, sum(out_tfuel_coins) as out_tfuel_coins, sum(out_tdrop_coins) as out_tdrop_coins, sum(usd_in) as usd_in, sum(usd_out) as usd_out, sum(usd_in - usd_out) as remaining');

        if ($days == '1D') {
            $query2->where('date', '>=', date('Y-m-d H:i:s', strtotime('-1 day')));
        } else if ($days == '3D') {
            $query2->where('date', '>=' , date('Y-m-d H:i:s', strtotime('-3 day')));
        } else if ($days == '7D') {
            $query2->where('date', '>=' , date('Y-m-d H:i:s', strtotime('-7 days')));
        } else if ($days == '30D') {
            $query2->where('date', '>=' , date('Y-m-d H:i:s', strtotime('-30 days')));
        }

        if ($currency == 'theta') {
            $query2->where('currency' , 'theta');
        } else if ($currency == 'tfuel') {
            $query2->where('currency' , 'tfuel');
        } else if ($currency == 'tdrop') {
            $query2->where('currency' , 'tdrop');
        }

        if ($sort == 'transactions') {
            $query2->orderByDesc('times');
        } else if ($sort == 'volume_in') {
            $query2->orderByDesc('usd_in');
        } else if ($sort == 'volume_out') {
            $query2->orderByDesc('usd_out');
        } else if ($sort == 'remaining') {
            $query2->orderByDesc('remaining');
        }

        $volumes = $query2->groupBy('account')->simplePaginate(Constants::PAGINATION_PAGE_LIMIT)
            ->withQueryString();

        return view('theta.volumes', [
            'volumes' => $volumes,
            'accounts' => $accounts,
            'days' => $days,
            'currency' => $currency,
            'sort' => $sort
        ]);
    }

    public function search() {
        $q = request('q');
        if (!empty($q)) {
            $accountAcount = DB::table('accounts')->whereRaw('(name LIKE ? OR tags LIKE ?)', ["%{$q}%", "%{$q}%"])->count();
            if ($accountAcount > 0) {
                return redirect('/accounts?tags=' . $q);
            }
            $response = Http::get(Constants::THETA_EXPLORER_API_URL . '/api/transaction/' . $q);
            if ($response->ok()) {
                return redirect('/transaction/' . $q);
            } else {
                return redirect('/account/' . $q);
            }
        }
        return redirect('/');
    }

    public function detector()
    {
        return view('theta.detector', [
            'data' => []
        ]);
    }
}
