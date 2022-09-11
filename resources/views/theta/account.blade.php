<x-layout title="Theta Account" pageName="account">
    <div class="account-page container-lg">
        <div class="information col-lg-6 mt-4 mb-3">
            <h2>Account Details</h2>
            <table class="table mt-3">
                <tr>
                    <th>ID</th>
                    <td class="text-break">
                        <div style="line-height: 1.5">{{ $account['id'] }} {{ isset($accounts[$account['id']]) ? '(' . $accounts[$account['id']]['name'] . ')' : '' }}</div>
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
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th scope="col" class="fit-cell">Type</th>
                        <th scope="col" class="text-end">Coins</th>
                        <th scope="col">Staker</th>
                        <th scope="col">Holder</th>
                        <th scope="col">Status</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($account['stakes'] as $stake)
                        <tr>
                            <td class="fit-cell">{{ Helper::getNodeName($stake['type']) }}</td>
                            <td class="text-end"><x-currency type="{{ $stake['currency'] }}" top="2"/> <span>{{ Helper::formatNumber($stake['coins'], 2, 'auto') }}</span></td>
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
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th scope="col" class="fit-cell"></th>
                        <th scope="col" class="text-center fit-cell">Coins</th>
                        <th scope="col" class="text-start">Staker</th>
                        <th scope="col">Holder</th>
                        <th scope="col" class="fit-cell"></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($account['stakes'] as $stake)
                        <tr>
                            <td class="align-middle text-center fit-cell">{{ Helper::getNodeName($stake['type']) }}</td>
                            <td class="text-center fit-cell"><x-currency type="{{ $stake['currency'] }}" top="2"/> {{ Helper::formatNumber($stake['coins'], 2, 'auto') }}</td>
                            <td class="truncate-cell"><a href="/account/{{ $stake['source'] }}">{{ strtolower($stake['source']) == strtolower($account['id']) ? 'Me' : (isset($accounts[$stake['source']]) ? $accounts[$stake['source']]['name'] : $stake['source']) }}</a></td>
                            <td class="truncate-cell"><a href="/account/{{ $stake['holder'] }}">{{ strtolower($stake['holder']) == strtolower($account['id']) ? 'Me' : (isset($accounts[$stake['holder']]) ? $accounts[$stake['holder']]['name'] : $stake['holder']) }}</a></td>
                            <td class="align-middle text-center fit-cell">{{ ucfirst($stake['status']) }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        @if (count($transactions) > 0)
            <div class="transactions d-none d-lg-block mt-4">
                <h5>Transactions</h5>
                <table class="table table-striped">
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
                            <td class="text-center fit-cell">{{ ucfirst($transaction->type) }}</td>
                            <td class="truncate-cell"><a href="/transaction/{{ $transaction->txn }}" class="text-decoration-none">{{ $transaction->txn }}</a></td>
                            <td class="truncate-cell">
                                @if (strtolower($transaction->from_account) == strtolower($account['id']))
                                    Me
                                @else
                                    <a href="/account/{{ $transaction->from_account }}" class="text-decoration-none">{{ isset($accounts[$transaction->from_account]) ? $accounts[$transaction->from_account]['name'] : $transaction->from_account }}</a>
                                @endif
                            </td>
                            <td class="truncate-cell">
                                @if (strtolower($transaction->to_account) == strtolower($account['id']))
                                    Me
                                @else
                                    <a href="/account/{{ $transaction->to_account }}" class="text-decoration-none">{{ isset($accounts[$transaction->to_account]) ? $accounts[$transaction->to_account]['name'] : $transaction->to_account }}</a>
                                @endif
                            </td>
                            <td class="text-end fit-cell"><x-currency type="{{ $transaction->currency }}" top="2"/> {{ Helper::formatNumber($transaction->coins, 2) }}</td>
                            <td class="text-end fit-cell">${{ number_format($transaction->usd, 2) }}</td>
                            <td class="text-center fit-cell">{{ $transaction->date }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                {{ $transactions->links() }} (Transactions in the last 30 days)
            </div>

            <div class="transactions mobile d-block d-lg-none">
                <h5>Transactions</h5>
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th scope="col" class="fit-cell"></th>
                        <th scope="col">From / To</th>
                        <th scope="col" class="text-end fit-cell">Amount</th>
                        <th scope="col" class="text-center fit-cell">Date</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($transactions as $transaction)
                        <tr>
                            <td class="align-middle text-center fit-cell ps-2 pe-2">
                                {{ ucfirst($transaction->type) }}
                            </td>
                            <td class="align-middle truncate-cell">
                                @if (strtolower($transaction->from_account) == strtolower($account['id']))
                                    Me
                                @else
                                    <a href="/account/{{ $transaction->from_account }}" class="text-decoration-none">{{ (isset($accounts[$transaction->from_account]) ? $accounts[$transaction->from_account]['name'] : $transaction->from_account) }}</a>
                                @endif
                                <br/>
                                @if (strtolower($transaction->to_account) == strtolower($account['id']))
                                    Me
                                @else
                                    <a href="/account/{{ $transaction->to_account }}" class="text-decoration-none">{{ (isset($accounts[$transaction->to_account]) ? $accounts[$transaction->to_account]['name'] : $transaction->to_account) }}</a>
                                @endif

                            </td>
                            <td class="text-end fit-cell ps-2 pe-2">
                                <x-currency type="{{ $transaction->currency }}" top="2"/> <a href="/transaction/{{ $transaction->txn }}" class="text-decoration-none">{{ Helper::formatNumber($transaction->coins, 1, 'auto') }}</a><br/>
                                (<span class="text-end">${{ Helper::formatNumber($transaction->usd, 1, 'auto') }}</span>)
                            </td>
                            <td class="text-center align-middle fit-cell ps-2 pe-2">{{ date('Y', strtotime($transaction->date)) }}<br/>{{ date('m-d', strtotime($transaction->date)) }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                {{ $transactions->links() }} (Transactions in the last 30 days)
            </div>
        @endif

        <a href="{{ Helper::makeThetaAccountURL($account['id']) }}" target="_blank" class="mt-2 d-block">View more on Theta Explorer</a>

    </div>
</x-layout>

