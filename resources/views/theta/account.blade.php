<x-layout title="Theta Account" pageName="account">
    <div class="account-page container-lg">
        <div class="information col-lg-6 mt-4 mb-3">
            <h2>Account Details</h2>
            <table class="table mt-3">
                <tr>
                    <th>ID</th>
                    <td class="text-break">
                        <div style="line-height: 1.5">{{ $account['id'] }} {{ isset($accounts[$account['id']]) ? '(' . $accounts[$account['id']]['name'] . ')' : '' }}</div>

                        @if ($whaleStatus != 'no')
                            @if ($whaleStatus == 'identified')
                               <div><span class="bi bi-check-circle-fill text-info"></span> Marked as <a href="/whales">whales</a></div>
                            @else
                                <div><span class="bi bi-lightning"></span> <a href="/whales/add/{{ $account['id'] }}">Mark this account as whales</a></div>
                            @endif
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>Balance</th>
                    <td class="text-break">
                        <div><x-currency type="theta"/> {{ Helper::formatNumber($account['balance']['theta'], 2) }} ({{ Helper::formatPrice($account['balance']['theta'] * $coins['THETA']['price'], 2) }})</div>
                        <div><x-currency type="tfuel"/> {{ Helper::formatNumber($account['balance']['tfuel'], 2) }} ({{ Helper::formatPrice($account['balance']['tfuel'] * $coins['TFUEL']['price'], 2) }})</div>
                    </td>
                </tr>
            </table>
        </div>

        @if (count($account['stakes']) > 0)
            <div class="stakes col-lg-8 d-none d-lg-block mt-4">
                <h4>Stakes</h4>
                <table class="table">
                    <thead>
                    <tr>
                        <th scope="col">Type</th>
                        <th scope="col" class="text-end">Coins</th>
                        <th scope="col">Staker</th>
                        <th scope="col">Holder</th>
                        <th scope="col">Status</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($account['stakes'] as $stake)
                        <tr>
                            <td>{{ Helper::getNodeName($stake['type']) }}</td>
                            <td class="text-end"><x-currency type="{{ $stake['currency'] }}" top="2"/> <span>{{ Helper::formatNumber($stake['coins'], 0) }}</span></td>
                            <td><a href="/account/{{ $stake['source'] }}">{{ strtolower($stake['source']) == strtolower($account['id']) ? 'Me' : (isset($accounts[$stake['source']]) ? $accounts[$stake['source']]['name'] : $stake['source']) }}</a></td>
                            <td><a href="/account/{{ $stake['holder'] }}">{{ strtolower($stake['holder']) == strtolower($account['id']) ? 'Me' : (isset($accounts[$stake['holder']]) ? $accounts[$stake['holder']]['name'] : $stake['holder']) }}</a></td>
                            <td>{{ ucfirst($stake['status']) }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <div class="stakes mobile d-block d-lg-none mt-4">
                <h4>Stakes</h4>
                <table class="table">
                    <thead>
                    <tr>
                        <th scope="col"></th>
                        <th scope="col" class="text-center">Coins</th>
                        <th scope="col" class="text-start">Staker</th>
                        <th scope="col">Holder</th>
                        <th scope="col">Status</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($account['stakes'] as $stake)
                        <tr>
                            <td>{{ Str::limit(Helper::getNodeName($stake['type']), 1, '-') }}</td>
                            <td class="text-end"><x-currency type="{{ $stake['currency'] }}" top="2"/> {{ Helper::formatNumber($stake['coins'], 0) }}</td>
                            <td><a href="/account/{{ $stake['source'] }}">{{ strtolower($stake['source']) == strtolower($account['id']) ? 'Me' : Str::limit(isset($accounts[$stake['source']]) ? $accounts[$stake['source']]['name'] : $stake['source'], 4, '..') }}</a></td>
                            <td><a href="/account/{{ $stake['holder'] }}">{{ strtolower($stake['holder']) == strtolower($account['id']) ? 'Me' : Str::limit(isset($accounts[$stake['holder']]) ? $accounts[$stake['holder']]['name'] : $stake['holder'], 4, '..') }}</a></td>
                            <td>{{ ucfirst($stake['status']) }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        @if (count($account['transactions']) > 0)
            <div class="transactions d-none d-lg-block mt-4">
                <h4>Transactions</h4>
                <table class="table table-striped">
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
                    @foreach ($account['transactions'] as $transaction)
                        <tr>
                            <td class="text-center">{{ ucfirst($transaction['type']) }}</td>
                            <td><a href="/transaction/{{ $transaction['id'] }}" class="text-decoration-none">{{ Str::limit($transaction['id'], 10) }}</a></td>
                            <td><a href="/account/{{ $transaction['from'] }}" class="text-decoration-none {{ isset($trackingAccounts[$transaction['from']]) ? 'text-dark' : '' }}">{{ strtolower($transaction['from']) == strtolower($account['id']) ? 'Me' : (isset($accounts[$transaction['from']]) ? $accounts[$transaction['from']]['name'] : Str::limit($transaction['from'], 10)) }}</a></td>
                            <td><a href="/account/{{ $transaction['to'] }}" class="text-decoration-none {{ isset($trackingAccounts[$transaction['to']]) ? 'text-dark' : '' }}">{{ strtolower($transaction['to']) == strtolower($account['id']) ? 'Me' : (isset($accounts[$transaction['to']]) ? $accounts[$transaction['to']]['name'] : Str::limit($transaction['to'], 10)) }}</a></td>
                            <td class="text-end"><x-currency type="{{ $transaction['currency'] }}" top="2"/> {{ Helper::formatNumber($transaction['coins'], 2) }}</td>
                            <td class="text-end {{ $transaction['usd'] > 100000 ? 'fw-bold text-danger' : '' }}">${{ number_format($transaction['usd'], 2) }}</td>
                            <td class="text-center">{{ $transaction['date'] }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <div class="transactions mobile d-block d-lg-none">
                <h4>Transactions</h4>
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th scope="col"></th>
                        <th scope="col">From</th>
                        <th scope="col">To</th>
                        <th scope="col" class="text-end">Amount</th>
                        <th scope="col" class="text-end">Value</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($account['transactions'] as $transaction)
                        <tr>
                            <td>{{ Str::limit(strtoupper($transaction['type']), '1', '-') }}</td>
                            <td><a href="/account/{{ $transaction['from'] }}" class="text-decoration-none">{{ strtolower($transaction['from']) == strtolower($account['id']) ? 'Me' : (isset($accounts[$transaction['from']]) ? Str::limit($accounts[$transaction['from']]['name'], 6) : Str::limit($transaction['from'], 6)) }}</a></td>
                            <td><a href="/account/{{ $transaction['to'] }}" class="text-decoration-none">{{ strtolower($transaction['to']) == strtolower($account['id']) ? 'Me' : (isset($accounts[$transaction['to']]) ? Str::limit($accounts[$transaction['to']]['name'], 6) : Str::limit($transaction['to'], 6)) }}</a></td>
                            <td class="text-end"><x-currency type="{{ $transaction['currency'] }}" top="2"/> <a href="/transaction/{{ $transaction['id'] }}" class="text-decoration-none">{{ Helper::formatNumber($transaction['coins'], 0) }}</a></td>
                            <td class="text-end {{ $transaction['usd'] > 100000 ? 'fw-bold text-danger' : '' }}">${{ number_format($transaction['usd'], 0) }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <a href="{{ Helper::makeThetaAccountURL($account['id']) }}" target="_blank">View on Theta Explorer</a>

    </div>
</x-layout>

