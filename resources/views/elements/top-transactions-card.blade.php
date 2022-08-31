<div class="card c1x top-transactions m-2">
    <h6 class="card-header">
        <span class="icon bi bi-brightness-high"></span>
        <span class="name ms-1">TOP TRANSACTIONS 24H</span>
    </h6>
    <div class="card-body scrollable">
        <div class="container">
            @foreach ($topTransactions as $transaction)
                @php
                    $extra = '';
                    $whale = '';
                    if (isset($accounts[$transaction['from_account']])) {
                        $extra = 'whale';
                        $whale = $accounts[$transaction['from_account']]['name'];
                    } else if (isset($accounts[$transaction['to_account']])) {
                        $extra = 'whale';
                        $whale = $accounts[$transaction['to_account']]['name'];
                    }
                @endphp
                <div class="row">
                    @if ($transaction['type'] == 'transfer')
                        <span class="bullet h-auto {{ $extra }}" title="{{ $whale }}">Transfer</span> <x-currency type="{{ $transaction['currency'] }}"/> <a href="{{ Helper::makeSiteTransactionURL($transaction['txn'], $transaction['currency']) }}" class="w-auto ps-1 pe-1">{{ number_format($transaction['coins'], 0) }} ({{ Helper::formatPrice($transaction['usd']) }})</a>
                    @elseif ($transaction['type'] == 'stake')
                        <span class="bullet h-auto">Stake</span> <x-currency type="{{ $transaction['currency'] }}"/> <a href="{{ Helper::makeSiteTransactionURL($transaction['txn'], $transaction['currency']) }}" class="w-auto ps-1 pe-1">{{ number_format($transaction['coins'], 0) }} ({{ Helper::formatPrice($transaction['usd']) }})</a>
                    @elseif ($transaction['type'] == 'unstake')
                        <span class="bullet h-auto">Unstake</span> <x-currency type="{{ $transaction['currency'] }}"/> <a href="{{ Helper::makeSiteAccountURL($transaction['to_account'], $transaction['currency']) }}" class="w-auto ps-1 pe-1">{{ number_format($transaction['coins'], 0) }} ({{ Helper::formatPrice($transaction['usd']) }})</a>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</div>
