<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Constants;
use App\Http\Controllers\Controller;
use App\Models\Holder;
use App\Models\Log;
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
        $search = request('search');
        $query = Validator::query();
        if (!empty($search)) {
            $query->where('holder', 'LIKE', "%{$search}%")->orWhere('name', 'LIKE', "%{$search}%");
        }
        $validators = $query->orderByDesc('amount')->get();
        return view('admin.network.validators', [
            'validators' => $validators,
            'search' => $search
        ]);
    }

    public function addValidator()
    {
        if (request()->isMethod('post')) {
            request()->validate([
                'holder' => ['bail', 'required', 'string', 'unique:validators'],
                'name' => ['bail', 'required', 'string'],
                'amount' => ['bail', 'required', 'numeric']
            ]);
            $data = request()->only('holder', 'name', 'amount');
            $data['chain'] = 'theta';
            $data['coin'] = 'theta';
            Validator::create($data);
            $this->thetaService->cacheValidators();
            return back()->with('message', 'Added successfully.');
        }
        return view('admin.network.add_validator');
    }

    public function editValidator($id)
    {
        $validator = Validator::find($id);
        if (request()->isMethod('post')) {
            request()->validate([
                'holder' => ['bail', 'required', 'string', Rule::unique('validators')->ignore($validator->holder, 'holder')],
                'name' => ['bail', 'required', 'string'],
                'amount' => ['bail', 'required', 'numeric']
            ]);
            $data = request()->only('holder', 'name', 'amount');
            $validator->update($data);
            $this->thetaService->cacheValidators();
            return back()->with('message', 'Edited successfully.');
        }
        return view('admin.network.edit_validator', ['validator' => $validator]);
    }

    public function deleteValidator($id)
    {
        Validator::destroy($id);
        $this->thetaService->cacheValidators();
        return back();
    }

    public function holders()
    {
        $search = request('search');
        $query = Holder::query();
        if (!empty($search)) {
            $query->where('code', 'LIKE', "%{$search}%")->orWhere('name', 'LIKE', "%{$search}%");
        }
        $holders = $query->orderByDesc('created_at')->paginate(Constants::PAGINATION_PAGE_LIMIT)->withQueryString();
        return view('admin.network.holders', [
            'holders' => $holders,
            'search' => $search
        ]);
    }

    public function addHolder()
    {
        if (request()->isMethod('post')) {
            request()->validate([
                'code' => ['bail', 'required', 'string', 'unique:holders'],
                'name' => ['bail', 'required', 'string'],
            ]);
            $data = request()->only('code', 'name');
            $data['chain'] = 'theta';
            Holder::create($data);
            $this->thetaService->cacheHolders();
            return back()->with('message', 'Added successfully.');
        }
        return view('admin.network.add_holder');
    }

    public function editHolder($id)
    {
        $holder = Holder::find($id);
        if (request()->isMethod('post')) {
            request()->validate([
                'code' => ['bail', 'required', 'string', Rule::unique('holders')->ignore($holder->code, 'code')],
                'name' => ['bail', 'required', 'string'],
            ]);
            $data = request()->only('code', 'name');
            $holder->update($data);
            $this->thetaService->cacheHolders();
            return back()->with('message', 'Edited successfully.');
        }
        return view('admin.network.edit_holder', ['holder' => $holder]);
    }

    public function deleteHolder($id)
    {
        Holder::destroy($id);
        $this->thetaService->cacheHolders();
        return back();
    }

    public function topActivists()
    {
        $search = request('search');
        $holders = $this->thetaService->getHolders();

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
            'holders' => $holders,
            'search' => $search
        ]);
    }

    public function transactions()
    {
        $sort = request('sort', 'large_value');
        $search = request('search');
        $holders = $this->thetaService->getHolders();
        $transactions = DB::table('transactions');
        $transactions->leftJoin('holders AS holders_1', 'transactions.from_account', '=', 'holders_1.code');
        $transactions->leftJoin('holders AS holders_2', 'transactions.to_account', '=', 'holders_2.code');
        $transactions->selectRaw('transactions.*, holders_1.name AS from_name, holders_2.name AS to_name, IF(holders_1.id IS NOT NULL OR holders_2.id IS NOT NULL, 1, 0) AS has_holder');
        if (!empty($search)) {
            $transactions->whereRaw("from_account LIKE '%{$search}%' OR holders_1.name LIKE '%{$search}%' OR to_account LIKE '%{$search}%' OR holders_2.name LIKE '%{$search}%'");
        }
        if (!empty($sort)) {
            if ($sort == 'large_value') {
                $transactions->orderByDesc('usd');
            } else if ($sort == 'latest_date') {
                $transactions->orderByDesc('date');
            } else if ($sort == 'verified_accounts_large_value') {
                $transactions->orderByDesc('has_holder')->orderByDesc('usd');
            } else if ($sort == 'verified_accounts_latest_date') {
                $transactions->orderByDesc('has_holder')->orderByDesc('date');
            }
        }
        $transactions = $transactions->paginate(Constants::PAGINATION_PAGE_LIMIT)->withQueryString();
        return view('admin.network.transactions', [
            'transactions' => $transactions,
            'holders' => $holders,
            'search' => $search,
            'sort' => $sort
        ]);
    }

    public function logs()
    {
        $logs = Log::query()->orderByDesc('created_at')->paginate(Constants::PAGINATION_PAGE_LIMIT)->withQueryString();
        return view('admin.network.logs', [
            'logs' => $logs
        ]);
    }
}
