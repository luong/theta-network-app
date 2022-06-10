<x-admin_layout pageName="top_transactions">
    <div class="top-transaction-page">
        <x-slot name="header">Top Transactions in 7 Days</x-slot>

        <div class="col-lg-6">
            <form method="get">
                <div class="input-group mb-3">
                    <input type="text" name="search" value="{{ $search }}" class="form-control" placeholder="Search by account">
                    <button class="btn btn-outline-secondary" type="submit">Search</button>
                </div>
            </form>
        </div>

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
                        <td><a href="{{ Helper::makeThetaAccountURL($transaction->from_account) }}" target="_blank">{{ $fromAccount }}</a></td>
                        <td><a href="{{ Helper::makeThetaAccountURL($transaction->to_account) }}" target="_blank">{{ $toAccount }}</a></td>
                        <td class="text-end">{{ number_format($transaction->amount, 0) }}</td>
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
                        <td><a href="{{ Helper::makeThetaAccountURL($transaction->from_account) }}" target="_blank">{{ Str::limit($fromAccount, 6) }}</a></td>
                        <td><a href="{{ Helper::makeThetaAccountURL($transaction->to_account) }}" target="_blank">{{ Str::limit($toAccount, 6) }}</a></td>
                        <td class="text-end">${{ number_format($transaction->usd, 2) }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            {{ $transactions->links() }}
        </div>

    </div>

</x-admin_layout>
