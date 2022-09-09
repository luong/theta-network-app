<x-layout title="Transactions" pageName="top_transactions">
    <div class="transactions-page">
        <form method="get">
        <div class="row col-lg-6 ms-auto me-auto mt-3 mb-2">
            <div class="col-6 col-md-3">
                <select name="type" class="form-select" onchange="this.form.submit()">
                    <option value="">Type</option>
                    <option value="transfer" {{ $type == 'transfer' ? 'selected' : '' }}>Transfer</option>
                    <option value="stake" {{ $type == 'stake' ? 'selected' : '' }}>Stake</option>
                    <option value="unstake" {{ $type == 'unstake' ? 'selected' : '' }}>Unstake</option>
                </select>
            </div>
            <div class="col-6 col-md-3">
                <select name="account" class="tags-select form-select" onchange="this.form.submit()">
                    <option value="">Account</option>
                    <option value="whales" {{ $account == 'whales' ? 'selected' : '' }}>Whales</option>
                    <option value="thetalabs" {{ $account == 'thetalabs' ? 'selected' : '' }}>ThetaLabs</option>
                    <option value="exchange" {{ $account == 'exchange' ? 'selected' : '' }}>Exchanges</option>
                    <option value="validator" {{ $account == 'validator' ? 'selected' : '' }}>Validators</option>
                </select>
            </div>
            <div class="col-6 col-md-3 mt-2 mt-md-0">
                <select name="currency" class="form-select" onchange="this.form.submit()">
                    <option value="">Currency</option>
                    <option value="theta" {{ $currency == 'theta' ? 'selected' : '' }}>Theta</option>
                    <option value="tfuel" {{ $currency == 'tfuel' ? 'selected' : '' }}>Tfuel</option>
                    <option value="tdrop" {{ $currency == 'tdrop' ? 'selected' : '' }}>Tdrop</option>
                </select>
            </div>
            <div class="col-6 col-md-3 mt-2 mt-md-0">
                <select name="sort" class="form-select" onchange="this.form.submit()">
                    <option value="latest_date" {{ $sort == 'latest_date' ? 'selected' : '' }}>Sort by latest date</option>
                    <option value="large_value" {{ $sort == 'large_value' ? 'selected' : '' }}>Sort by large value</option>
                </select>
            </div>
        </div>
        </form>

        <div class="container transactions ps-0 pe-0 d-none d-lg-block mt-4">
            <table class="table table-striped table-sm align-middle w-100">
                <thead>
                <tr>
                    <th scope="col" class="text-center">Type</th>
                    <th scope="col">TXN Hash</th>
                    <th scope="col">From</th>
                    <th scope="col">To</th>
                    <th scope="col" class="text-end">Amount</th>
                    <th scope="col" class="text-end">Value</th>
                    <th scope="col" class="text-center">Date</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($transactions as $transaction)
                    <tr>
                        <td class="text-center">{{ ucfirst($transaction->type) }}</td>
                        <td><a href="/transaction/{{ $transaction->txn }}" class="text-decoration-none">{{ Str::limit($transaction->txn, 10) }}</a></td>
                        <td><a href="/account/{{ $transaction->from_account }}" class="text-decoration-none {{ isset($trackingAccounts[$transaction->from_account]) ? 'text-success' : '' }}">{{ isset($accounts[$transaction->from_account]) ? $accounts[$transaction->from_account]['name'] : Str::limit($transaction->from_account, 10) }}</a></td>
                        <td><a href="/account/{{ $transaction->to_account }}" class="text-decoration-none {{ isset($trackingAccounts[$transaction->to_account]) ? 'text-success' : '' }}">{{ isset($accounts[$transaction->to_account]) ? $accounts[$transaction->to_account]['name'] : Str::limit($transaction->to_account, 10) }}</a></td>
                        <td class="text-end"><x-currency type="{{ $transaction->currency }}" top="2"/> {{ Helper::formatNumber($transaction->coins, 2) }}</td>
                        <td class="text-end {{ $transaction->usd > 100000 ? 'fw-bold text-danger' : '' }}">${{ number_format($transaction->usd, 2) }}</td>
                        <td class="text-center">{{ $transaction->date }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            {{ $transactions->links() }} (Transactions collected in 7 days)
        </div>

        <div class="container transactions mobile d-block d-lg-none">
            <table class="table table-striped">
                <thead>
                <tr>
                    <th scope="col" style="width:35px"></th>
                    <th scope="col">From / To</th>
                    <th scope="col" class="text-end">Amount</th>
                    <th scope="col" class="text-center" style="width:80px">Date</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($transactions as $transaction)
                    <tr>
                        <td class="align-middle text-center">
                            {{ ucfirst($transaction->type)[0] }}
                        </td>
                        <td class="align-middle">
                            <a href="/account/{{ $transaction->to_account }}" class="text-decoration-none">{{ isset($accounts[$transaction->from_account]) ? Str::limit($accounts[$transaction->from_account]['name'], 16, '..') : Str::limit($transaction->from_account, 16, '..') }}</a>
                            <br/>
                            <a href="/account/{{ $transaction->to_account }}" class="text-decoration-none">{{ isset($accounts[$transaction->to_account]) ? Str::limit($accounts[$transaction->to_account]['name'], 16, '..') : Str::limit($transaction->to_account, 16, '..') }}</a>
                        </td>
                        <td class="text-end">
                            <x-currency type="{{ $transaction->currency }}" top="2"/> <a href="/transaction/{{ $transaction->txn }}" class="text-decoration-none">{{ Helper::formatNumber($transaction->coins, 1, 'auto') }}</a><br/>
                            (<span class="text-end {{ $transaction->usd > 100000 ? 'fw-bold text-danger' : '' }}">${{ Helper::formatNumber($transaction->usd, 2, 'auto') }}</span>)
                        </td>
                        <td class="text-center align-middle">{{ date('Y', strtotime($transaction->date)) }}<br/>{{ date('m-d', strtotime($transaction->date)) }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            {{ $transactions->links() }} (Transactions collected in 7 days)
        </div>

    </div>

</x-layout>
