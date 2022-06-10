<x-layout title="ThetaNetworkApp" pageName="account">
    <div class="account-page container-lg">
        <div class="information col-lg-6 card mt-4 mb-3">
            <h5 class="card-header">Account Details</h5>
            <div class="card-body">
                <div class="d-flex justify-content-start">
                    <div class="fw-bold">Address: </div>
                    <div class="ms-2 text-truncate"><span class="d-none d-lg-inline-block">{{ $account['id'] }}</span><span class="d-inline-block d-lg-none">{{ Str::limit($account['id'], 7) }}</span> {{ isset($holders[$account['id']]) ? '(' . $holders[$account['id']]['name'] . ')' : '' }}</div>
                </div>
                <div class="d-flex justify-content-start">
                    <div class="fw-bold">Balance: </div>
                    <div class="ms-2">
                        <div><img src="/images/theta_flat.png" width="20"/> {{ Helper::formatNumber($account['balance']['theta'], 2) }} theta [{{ Helper::formatPrice($account['balance']['theta'] * $coins['THETA']['price'], 2) }}]</div>
                        <div><img src="/images/tfuel_flat.png" width="20"/> {{ Helper::formatNumber($account['balance']['tfuel'], 2) }} tfuel [{{ Helper::formatPrice($account['balance']['tfuel'] * $coins['TFUEL']['price'], 2) }}]</div>
                    </div>
                </div>
            </div>
        </div>

        <a href="{{ Helper::makeThetaAccountURL($account['id']) }}" target="_blank">View on Theta Explorer</a>

        <div class="transactions d-none d-lg-block">
            <table class="table table-striped">
                <thead>
                <tr>
                    <th scope="col" class="text-center">Type</th>
                    <th scope="col">TXN Hash</th>
                    <th scope="col">From</th>
                    <th scope="col">To</th>
                    <th scope="col" class="text-end">Amount</th>
                    <th scope="col" class="text-center">Currency</th>
                    <th scope="col" class="text-end">Value</th>
                    <th scope="col" class="text-center">Date</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($account['transactions'] as $transaction)
                    <tr>
                        <td class="text-center">{{ ucfirst($transaction['type']) }}</td>
                        <td><a href="{{ Helper::makeThetaTransactionURL($transaction['id']) }}" target="_blank" class="text-decoration-none">{{ Str::limit($transaction['id'], 10) }}</a></td>
                        <td><a href="/account/{{ $transaction['from'] }}" class="text-decoration-none">{{ $transaction['from'] == $account['id'] ? 'Me' : (isset($holders[$transaction['from']]) ? $holders[$transaction['from']]['name'] : Str::limit($transaction['from'], 10)) }}</a></td>
                        <td><a href="/account/{{ $transaction['to'] }}" class="text-decoration-none">{{ $transaction['to'] == $account['id'] ? 'Me' : (isset($holders[$transaction['to']]) ? $holders[$transaction['to']]['name'] : Str::limit($transaction['to'], 10)) }}</a></td>
                        <td class="text-end">{{ Helper::formatNumber($transaction['coins'], 2) }}</td>
                        <td class="text-center">{{ $transaction['currency'] }}</td>
                        <td class="text-end {{ $transaction['usd'] > 100000 ? 'fw-bold text-danger' : '' }}">${{ number_format($transaction['usd'], 2) }}</td>
                        <td class="text-center">{{ $transaction['date'] }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <div class="transactions d-block d-lg-none">
            <table class="table table-striped">
                <thead>
                <tr>
                    <th scope="col">From</th>
                    <th scope="col">To</th>
                    <th scope="col" class="text-end">Amount</th>
                    <th scope="col" class="text-end">Value</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($account['transactions'] as $transaction)
                    <tr>
                        <td><a href="/account/{{ $transaction['from'] }}" class="text-decoration-none">{{ $transaction['from'] == $account['id'] ? 'Me' : (isset($holders[$transaction['from']]) ? $holders[$transaction['from']]['name'] : Str::limit($transaction['from'], 5)) }}</a></td>
                        <td><a href="/account/{{ $transaction['to'] }}" class="text-decoration-none">{{ $transaction['to'] == $account['id'] ? 'Me' : (isset($holders[$transaction['to']]) ? $holders[$transaction['to']]['name'] : Str::limit($transaction['to'], 5)) }}</a></td>
                        <td class="text-end">{{ Helper::formatNumber($transaction['coins'], 0) }} {{ $transaction['currency'] }}</td>
                        <td class="text-end {{ $transaction['usd'] > 100000 ? 'fw-bold text-danger' : '' }}">${{ number_format($transaction['usd'], 0) }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

    </div>
</x-layout>

