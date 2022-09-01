<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Constants;
use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Log;
use App\Models\Stake;
use App\Models\Transaction;
use App\Models\Validator;
use App\Services\ThetaService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class NetworkController extends Controller
{

    private $thetaService;

    public function __construct(ThetaService $thetaService) {
        $this->thetaService = $thetaService;
    }

    public function validators()
    {
        $validators = $this->thetaService->getValidators();
        $accounts = $this->thetaService->getAccounts();
        return view('admin.network.validators', [
            'validators' => $validators,
            'accounts' => $accounts
        ]);
    }

    public function accounts()
    {
        $search = request('search');
        $query = Account::query();
        if (!empty($search)) {
            $query->where('code', 'LIKE', "%{$search}%")->orWhere('name', 'LIKE', "%{$search}%");
        }
        $accounts = $query->orderBy('name')->paginate(1000)->withQueryString();
        return view('admin.network.accounts', [
            'accounts' => $accounts,
            'search' => $search
        ]);
    }

    public function addAccount()
    {
        if (request()->isMethod('post')) {
            request()->validate([
                'code' => ['bail', 'required', 'string', 'unique:accounts'],
                'name' => ['bail', 'required', 'string'],
            ]);
            $data = request()->only('code', 'name');
            Account::create($data);
            $this->thetaService->addTrackingAccount($data['code'], null, null, true);
            $this->thetaService->cacheAccounts();
            $this->thetaService->cacheValidators();
            return back()->with('message', 'Added successfully.');
        }
        return view('admin.network.add_account');
    }

    public function editAccount($id)
    {
        $account = Account::find($id);
        if (request()->isMethod('post')) {
            request()->validate([
                'code' => ['bail', 'required', 'string', Rule::unique('accounts')->ignore($account->code, 'code')],
                'name' => ['bail', 'required', 'string'],
            ]);
            $data = request()->only('code', 'name');
            $account->update($data);
            $this->thetaService->addTrackingAccount($data['code']);
            $this->thetaService->cacheAccounts();
            $this->thetaService->cacheValidators();
            return back()->with('message', 'Edited successfully.');
        }
        return view('admin.network.edit_account', ['account' => $account]);
    }

    public function deleteAccount($id)
    {
        Account::destroy($id);
        $this->thetaService->cacheAccounts();
        return back();
    }

    public function topActivists()
    {
        $search = request('search');
        $accounts = $this->thetaService->getAccounts();

        $query1 = DB::table('transactions')->select('from_account as account', DB::raw('0 as usd_in'), 'usd as usd_out', 'usd')
            ->union(DB::table('transactions')->select('to_account as account', 'usd as usd_in', DB::raw('0 as usd_out'), 'usd'));

        $query2 = Transaction::query()->fromSub($query1, 't1')
            ->selectRaw('account, count(*) as times, sum(usd_in) as usd_in, sum(usd_out) as usd_out, sum(usd) as usd')
            ->groupBy('account');

        if (!empty($search)) {
            $query2->having('account', '=', $search);
        }

        $activists = $query2->orderByDesc('usd')
            ->paginate(Constants::PAGINATION_PAGE_LIMIT)
            ->withQueryString();

        return view('admin.network.top_activists', [
            'activists' => $activists,
            'accounts' => $accounts,
            'search' => $search
        ]);
    }

    public function transactions()
    {
        $type = request('type');
        $sort = request('sort', 'large_value');
        $search = request('search');
        $accounts = $this->thetaService->getAccounts();
        $transactions = DB::table('transactions');
        $transactions->leftJoin('accounts AS accounts_1', 'transactions.from_account', '=', 'accounts_1.code');
        $transactions->leftJoin('accounts AS accounts_2', 'transactions.to_account', '=', 'accounts_2.code');
        $transactions->selectRaw('transactions.*, accounts_1.name AS from_name, accounts_2.name AS to_name, IF(accounts_1.id IS NOT NULL OR accounts_2.id IS NOT NULL, 1, 0) AS has_account');
        if (!empty($search)) {
            $transactions->whereRaw("(from_account LIKE '%{$search}%' OR accounts_1.name LIKE '%{$search}%' OR to_account LIKE '%{$search}%' OR accounts_2.name LIKE '%{$search}%')");
        }
        if (!empty($type)) {
            $transactions->where('type', $type);
        }
        if (!empty($sort)) {
            if ($sort == 'large_value') {
                $transactions->orderByDesc('usd');
            } else if ($sort == 'latest_date') {
                $transactions->orderByDesc('date');
            } else if ($sort == 'verified_accounts_large_value') {
                $transactions->orderByDesc('has_account')->orderByDesc('usd');
            } else if ($sort == 'verified_accounts_latest_date') {
                $transactions->orderByDesc('has_account')->orderByDesc('date');
            }
        }
        $transactions = $transactions->paginate(Constants::PAGINATION_PAGE_LIMIT)->withQueryString();
        return view('admin.network.transactions', [
            'transactions' => $transactions,
            'accounts' => $accounts,
            'search' => $search,
            'sort' => $sort,
            'type' => $type
        ]);
    }

    public function logs()
    {
        $logs = Log::query()->orderByDesc('created_at')->paginate(Constants::PAGINATION_PAGE_LIMIT)->withQueryString();
        return view('admin.network.logs', [
            'logs' => $logs
        ]);
    }

    public function stakes()
    {
        $type = request('type');
        $withdrawn = request('withdrawn');
        $search = request('search');

        $query = DB::table('stakes');
        $query->leftJoin('accounts AS accounts_1', 'stakes.holder', '=', 'accounts_1.code');
        $query->leftJoin('accounts AS accounts_2', 'stakes.source', '=', 'accounts_2.code');
        $query->selectRaw('stakes.*, accounts_1.name AS holder_name, accounts_2.name AS source_name');
        if (!empty($type)) {
            $query->where('type', $type);
        }
        if (!empty($withdrawn)) {
            $query->where('withdrawn', $withdrawn == 'yes' ? 1 : 0);
        }
        if (!empty($search)) {
            $query->whereRaw("(holder LIKE '%{$search}%' OR accounts_1.name LIKE '%{$search}%' OR source LIKE '%{$search}%' OR accounts_2.name LIKE '%{$search}%')");
        }
        $stakes = $query->orderByDesc('usd')->paginate(Constants::PAGINATION_PAGE_LIMIT)->withQueryString();

        $accounts = $this->thetaService->getAccounts();
        return view('admin.network.stakes', [
            'stakes' => $stakes,
            'accounts' => $accounts,
            'type' => $type,
            'withdrawn' => $withdrawn,
            'search' => $search,
            'networkInfo' => $this->thetaService->getNetworkInfo()
        ]);
    }
}
