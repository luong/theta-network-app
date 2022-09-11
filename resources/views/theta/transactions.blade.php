<x-layout title="Transactions" pageName="top_transactions">
    <div class="transactions-page">
        <form method="get">
        <div class="row col-lg-6 ms-auto me-auto mt-3 mb-2">
            <div class="col-4 col-lg-2">
                <select name="type" class="form-select" onchange="this.form.submit()">
                    <option value="">Type</option>
                    <option value="transfer" {{ $type == 'transfer' ? 'selected' : '' }}>Transfer</option>
                    <option value="stake" {{ $type == 'stake' ? 'selected' : '' }}>Stake</option>
                    <option value="unstake" {{ $type == 'unstake' ? 'selected' : '' }}>Unstake</option>
                </select>
            </div>
            <div class="col-4 col-lg-2">
                <select name="account" class="tags-select form-select" onchange="this.form.submit()">
                    <option value="">Account</option>
                    <option value="whales" {{ $account == 'whales' ? 'selected' : '' }}>Whales</option>
                    <option value="thetalabs" {{ $account == 'thetalabs' ? 'selected' : '' }}>ThetaLabs</option>
                    <option value="exchange" {{ $account == 'exchange' ? 'selected' : '' }}>Exchanges</option>
                    <option value="validator" {{ $account == 'validator' ? 'selected' : '' }}>Validators</option>
                </select>
            </div>
            <div class="col-4 col-lg-3 mt-lg-0">
                <select name="currency" class="form-select" onchange="this.form.submit()">
                    <option value="">Currency</option>
                    <option value="theta" {{ $currency == 'theta' ? 'selected' : '' }}>Theta</option>
                    <option value="tfuel" {{ $currency == 'tfuel' ? 'selected' : '' }}>Tfuel</option>
                    <option value="tdrop" {{ $currency == 'tdrop' ? 'selected' : '' }}>Tdrop</option>
                </select>
            </div>
            <div class="col-6 col-lg-2 mt-2 mt-lg-0">
                <select name="days" class="form-select" onchange="this.form.submit()">
                    <option value="1D" {{ $days == '1D' ? 'selected' : '' }}>1 Day</option>
                    <option value="7D" {{ $days == '7D' ? 'selected' : '' }}>7 Days</option>
                    <option value="30D" {{ $days == '30D' ? 'selected' : '' }}>30 Days</option>
                </select>
            </div>
            <div class="col-6 col-lg-3 mt-2 mt-lg-0">
                <select name="sort" class="form-select" onchange="this.form.submit()">
                    <option value="latest_date" {{ $sort == 'latest_date' ? 'selected' : '' }}>By latest date</option>
                    <option value="large_value" {{ $sort == 'large_value' ? 'selected' : '' }}>By large value</option>
                </select>
            </div>
        </div>
        </form>

        <div class="container transactions ps-0 pe-0 d-none d-lg-block mt-4">
            <table class="table table-striped table-sm align-middle w-100">
                <thead>
                <tr>
                    <th scope="col" class="text-center fit-cell">Type</th>
                    <th scope="col">TXN Hash</th>
                    <th scope="col">From</th>
                    <th scope="col">To</th>
                    <th scope="col" class="text-end fit-cell">Amount</th>
                    <th scope="col" class="text-end fit-cell">Value</th>
                    <th scope="col" class="text-center fit-cell">Date</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($transactions as $transaction)
                    <tr>
                        <td class="text-center col-auto">{{ ucfirst($transaction->type) }}</td>
                        <td class="truncate-cell"><a href="/transaction/{{ $transaction->txn }}" class="text-decoration-none">{{ $transaction->txn }}</a></td>
                        <td class="truncate-cell"><a href="/account/{{ $transaction->from_account }}" class="text-decoration-none {{ isset($trackingAccounts[$transaction->from_account]) ? 'text-success' : '' }}">{{ isset($accounts[$transaction->from_account]) ? $accounts[$transaction->from_account]['name'] : $transaction->from_account }}</a></td>
                        <td class="truncate-cell"><a href="/account/{{ $transaction->to_account }}" class="text-decoration-none {{ isset($trackingAccounts[$transaction->to_account]) ? 'text-success' : '' }}">{{ isset($accounts[$transaction->to_account]) ? $accounts[$transaction->to_account]['name'] : $transaction->to_account }}</a></td>
                        <td class="text-end fit-cell"><x-currency type="{{ $transaction->currency }}" top="2"/> {{ Helper::formatNumber($transaction->coins, 2) }}</td>
                        <td class="text-end fit-cell">${{ number_format($transaction->usd, 2) }}</td>
                        <td class="text-center fit-cell">{{ $transaction->date }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            {{ $transactions->links() }} (Transactions in the last 30 days)
        </div>

        <div class="container transactions mobile d-block d-lg-none">
            <table class="table table-striped">
                <thead>
                <tr>
                    <th scope="col" class="fit-cell"></th>
                    <th scope="col" class="col-6">From / To</th>
                    <th scope="col" class="text-end fit-cell">Amount</th>
                    <th scope="col" class="text-center fit-cell">Date</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($transactions as $transaction)
                    <tr>
                        <td class="align-middle text-center fit-cell ps-1 pe-1">
                            {{ ucfirst($transaction->type) }}
                        </td>
                        <td class="align-middle" style="max-width: 1px;text-overflow: ellipsis;overflow: hidden;white-space: nowrap;">
                            <a href="/account/{{ $transaction->from_account }}" class="text-decoration-none">{{ isset($accounts[$transaction->from_account]) ? $accounts[$transaction->from_account]['name'] : $transaction->from_account }}</a>
                            <br/>
                            <a href="/account/{{ $transaction->to_account }}" class="text-decoration-none">{{ isset($accounts[$transaction->to_account]) ? $accounts[$transaction->to_account]['name'] : $transaction->to_account }}</a>
                        </td>
                        <td class="text-end fit-cell">
                            <x-currency type="{{ $transaction->currency }}" top="2"/> <a href="/transaction/{{ $transaction->txn }}" class="text-decoration-none">{{ Helper::formatNumber($transaction->coins, 1, 'auto') }}</a><br/>
                            (<span class="text-end">${{ Helper::formatNumber($transaction->usd, 1, 'auto') }}</span>)
                        </td>
                        <td class="text-center align-middle fit-cell">{{ date('Y', strtotime($transaction->date)) }}<br/>{{ date('m-d', strtotime($transaction->date)) }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            {{ $transactions->links() }} (Transactions in the last 30 days)
        </div>

    </div>

</x-layout>
