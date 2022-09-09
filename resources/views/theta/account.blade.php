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
                               <div><span class="bi bi-check-circle-fill text-info"></span> Marked as whales</div>
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
                        <div><x-currency type="tdrop"/> {{ Helper::formatNumber($account['balance']['tdrop'], 2) }} ({{ Helper::formatPrice($account['balance']['tdrop'] * $coins['TDROP']['price'], 2) }})</div>
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
                            <td>
                                @if (strtolower($stake['source']) == strtolower($account['id']))
                                    Me
                                @else
                                    <a href="/account/{{ $stake['source'] }}">{{ isset($accounts[$stake['source']]) ? $accounts[$stake['source']]['name'] : $stake['source'] }}</a>
                                @endif
                            </td>
                            <td>
                                @if (strtolower($stake['holder']) == strtolower($account['id']))
                                    Me
                                @else
                                    <a href="/account/{{ $stake['holder'] }}">{{ isset($accounts[$stake['holder']]) ? $accounts[$stake['holder']]['name'] : $stake['holder'] }}</a>
                                @endif
                            </td>
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
                        <th scope="col" style="width:40px"></th>
                        <th scope="col" class="text-center">Coins</th>
                        <th scope="col" class="text-start">Staker</th>
                        <th scope="col">Holder</th>
                        <th scope="col" style="width:40px"></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($account['stakes'] as $stake)
                        <tr>
                            <td class="align-middle text-center">{{ Helper::getNodeName($stake['type'])[0] }}</td>
                            <td class="text-center"><x-currency type="{{ $stake['currency'] }}" top="2"/> {{ Helper::formatNumber($stake['coins'], 0, 'auto') }}</td>
                            <td><a href="/account/{{ $stake['source'] }}">{{ strtolower($stake['source']) == strtolower($account['id']) ? 'Me' : Str::limit(isset($accounts[$stake['source']]) ? $accounts[$stake['source']]['name'] : $stake['source'], 10, '..') }}</a></td>
                            <td><a href="/account/{{ $stake['holder'] }}">{{ strtolower($stake['holder']) == strtolower($account['id']) ? 'Me' : Str::limit(isset($accounts[$stake['holder']]) ? $accounts[$stake['holder']]['name'] : $stake['holder'], 10, '..') }}</a></td>
                            <td class="align-middle text-center">{{ ucfirst($stake['status'])[0] }}</td>
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
                            <td><a href="/account/{{ $transaction['from'] }}" class="text-decoration-none {{ isset($trackingAccounts[$transaction['from']]) ? 'text-success' : '' }}">{{ strtolower($transaction['from']) == strtolower($account['id']) ? 'Me' : (isset($accounts[$transaction['from']]) ? $accounts[$transaction['from']]['name'] : Str::limit($transaction['from'], 10)) }}</a></td>
                            <td><a href="/account/{{ $transaction['to'] }}" class="text-decoration-none {{ isset($trackingAccounts[$transaction['to']]) ? 'text-success' : '' }}">{{ strtolower($transaction['to']) == strtolower($account['id']) ? 'Me' : (isset($accounts[$transaction['to']]) ? $accounts[$transaction['to']]['name'] : Str::limit($transaction['to'], 10)) }}</a></td>
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
                        <th scope="col" style="width:40px"></th>
                        <th scope="col">From / To</th>
                        <th scope="col" class="text-end">Amount</th>
                        <th scope="col" class="text-center" style="width:70px">Date</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($account['transactions'] as $transaction)
                        <tr>
                            <td class="align-middle text-center">
                                {{ ucfirst($transaction['type'])[0] }}
                            </td>
                            <td class="align-middle">
                                @if (strtolower($transaction['from']) == strtolower($account['id']))
                                    Me
                                @else
                                    <a href="/account/{{ $transaction['from'] }}" class="text-decoration-none">{{ (isset($accounts[$transaction['from']]) ? Str::limit($accounts[$transaction['from']]['name'], 14, '..') : Str::limit($transaction['from'], 14, '..')) }}</a>
                                @endif
                                <br/>
                                @if (strtolower($transaction['to']) == strtolower($account['id']))
                                    Me
                                @else
                                    <a href="/account/{{ $transaction['to'] }}" class="text-decoration-none">{{ (isset($accounts[$transaction['to']]) ? Str::limit($accounts[$transaction['to']]['name'], 14, '..') : Str::limit($transaction['to'], 14, '..')) }}</a>
                                @endif

                            </td>
                            <td class="text-end">
                                <x-currency type="{{ $transaction['currency'] }}" top="2"/> <a href="/transaction/{{ $transaction['id'] }}" class="text-decoration-none">{{ Helper::formatNumber($transaction['coins'], 1, 'auto') }}</a><br/>
                                (<span class="text-end {{ $transaction['usd'] > 100000 ? 'fw-bold text-danger' : '' }}">${{ Helper::formatNumber($transaction['usd'], 1, 'auto') }}</span>)
                            </td>
                            <td class="text-center align-middle">{{ date('Y', strtotime($transaction['date'])) }}<br/>{{ date('m-d', strtotime($transaction['date'])) }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <a href="{{ Helper::makeThetaAccountURL($account['id']) }}" target="_blank">View on Theta Explorer</a>

    </div>
</x-layout>

