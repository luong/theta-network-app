<x-admin_layout pageName="top_transactions">
    <div class="top-transaction-page">
        <x-slot name="header">Transactions</x-slot>

        <form method="get">
        <div class="col-lg-6 row">
            <div class="col col-5">
                <select name="sort" class="form-select" onchange="this.form.submit()">
                    <option value="large_value" {{ $sort == 'large_value' ? 'selected' : '' }}>Large value</option>
                    <option value="latest_date" {{ $sort == 'latest_date' ? 'selected' : '' }}>Latest date</option>
                    <option value="verified_accounts_large_value" {{ $sort == 'verified_accounts_large_value' ? 'selected' : '' }}>Verified + Large value</option>
                    <option value="verified_accounts_latest_date" {{ $sort == 'verified_accounts_latest_date' ? 'selected' : '' }}>Verified + Latest date</option>
                </select>
            </div>
            <div class="col col-7">
                <div class="input-group mb-3">
                    <input type="text" name="search" value="{{ $search }}" class="form-control" placeholder="Search by account">
                    <button class="btn btn-outline-secondary" type="submit">Search</button>
                </div>
            </div>
        </div>
        </form>


        <div class="container-sm ms-0 ps-0 me-0 pe-0 d-none d-lg-block">
            <table class="table table-striped table-sm align-middle">
                <thead>
                <tr>
                    <th scope="col">From ({{ $transactions->total() }})</th>
                    <th scope="col">To</th>
                    <th scope="col" class="text-end">Amount</th>
                    <th scope="col" class="text-center">Currency</th>
                    <th scope="col" class="text-end">USD</th>
                    <th scope="col" class="text-center">Date</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($transactions as $transaction)
                    @php
                        $fromAccount = isset($holders[$transaction->from_account]) ? $holders[$transaction->from_account]['name'] : $transaction->from_account;
                        $toAccount = isset($holders[$transaction->to_account]) ? $holders[$transaction->to_account]['name'] : $transaction->to_account;
                    @endphp
                    <tr>
                        <td><a href="/account/{{ $transaction->from_account }}">{{ $fromAccount }}</a></td>
                        <td><a href="/account/{{ $transaction->to_account }}">{{ $toAccount }}</a></td>
                        <td class="text-end"><a href="/transaction/{{ $transaction->txn }}">{{ number_format($transaction->amount, 0) }}</a></td>
                        <td class="text-center">{{ $transaction->currency }}</td>
                        <td class="text-end">${{ number_format($transaction->usd, 2) }}</td>
                        <td class="text-center">{{ $transaction->date }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            {{ $transactions->links() }}
        </div>

        <div class="container-sm ms-0 ps-0 me-0 pe-0 d-block d-lg-none">
            <table class="table table-striped table-sm align-middle">
                <thead>
                <tr>
                    <th scope="col">From ({{ $transactions->total() }})</th>
                    <th scope="col">To</th>
                    <th scope="col" class="text-end">USD</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($transactions as $transaction)
                    @php
                        $fromAccount = isset($holders[$transaction->from_account]) ? $holders[$transaction->from_account]['name'] : $transaction->from_account;
                        $toAccount = isset($holders[$transaction->to_account]) ? $holders[$transaction->to_account]['name'] : $transaction->to_account;
                    @endphp
                    <tr>
                        <td><a href="/account/{{ $transaction->from_account }}">{{ Str::limit($fromAccount, 6) }}</a></td>
                        <td><a href="/account/{{ $transaction->to_account }}">{{ Str::limit($toAccount, 6) }}</a></td>
                        <td class="text-end"><a href="/transaction/{{ $transaction->txn }}">${{ number_format($transaction->usd, 2) }}</a></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            {{ $transactions->links() }}
        </div>

    </div>

</x-admin_layout>
