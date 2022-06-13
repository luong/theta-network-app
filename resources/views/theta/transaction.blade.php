<x-layout title="ThetaNetworkApp" pageName="account">
    <div class="account-page container-lg">
        <div class="information col-lg-7 mt-4 mb-3">
            <h2>Transaction Details</h2>
            <table class="table mt-3">
                <tr>
                    <th>ID</th>
                    <td class="text-break">{{ $transaction['id'] }}</td>
                </tr>
                <tr>
                    <th>Type</th>
                    <td>{{ ucfirst($transaction['type']) }}</td>
                </tr>
                <tr>
                    <th>Block</th>
                    <td>{{ $transaction['block_height'] }}</td>
                </tr>
                <tr>
                    <th>Date</th>
                    <td>{{ date('Y-m-d H:i:s', $transaction['timestamp']) }}</td>
                </tr>
                <tr>
                    <th>From</th>
                    <td class="text-break"><a href="/account/{{ $transaction['from_account'] }}">{{ $transaction['from_account'] }}</a> {{ isset($accounts[$transaction['from_account']]) ? '(' . $accounts[$transaction['from_account']]['name'] . ')' : '' }}</td>
                </tr>
                <tr>
                    <th>To</th>
                    <td class="text-break"><a href="/account/{{ $transaction['to_account'] }}">{{ $transaction['to_account'] }}</a> {{ isset($accounts[$transaction['to_account']]) ? '(' . $accounts[$transaction['to_account']]['name'] . ')' : '' }}</td>
                </tr>
                <tr>
                    <th>Amount</th>
                    <td>{{ Helper::formatNumber($transaction['coins'], 4) }} <img class="currency-ico" src="/images/{{ $transaction['currency'] }}_flat.png"/> ({{ Helper::formatPrice($transaction['coins'] * ($transaction['currency'] == 'theta' ? $coins['THETA']['price'] : $coins['TFUEL']['price']), 2) }})</td>
                </tr>
                <tr>
                    <th>Fee</th>
                    <td>{{ $transaction['fee'] }} <img class="currency-ico" src="/images/tfuel_flat.png"/></td>
                </tr>
            </table>
        </div>

        <a href="{{ Helper::makeThetaTransactionURL($transaction['id']) }}" target="_blank">View on Theta Explorer</a>

    </div>
</x-layout>

