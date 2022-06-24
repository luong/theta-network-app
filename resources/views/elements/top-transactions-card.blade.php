<div class="card c1x top-transactions m-2">
    <h6 class="card-header">
        <span class="icon bi bi-brightness-high"></span>
        <span class="name ms-1">TOP TRANSACTIONS IN 24H</span>
    </h6>
    <div class="card-body scrollable">
        <div class="container">
            @foreach ($topTransactions as $transaction)
                <div class="row">
                    @if ($transaction['type'] == 'transfer')
                        <span class="bullet h-auto">Transfer</span><a href="/transaction/{{ $transaction['txn'] }}" class="w-auto p-0">{{ number_format($transaction['coins'], 0) }} <img class="currency-ico" src="/images/{{ $transaction['currency'] }}_flat.png"/> ({{ Helper::formatPrice($transaction['usd']) }})</a>
                    @elseif ($transaction['type'] == 'stake')
                        <span class="bullet h-auto">Stake</span><a href="/transaction/{{ $transaction['txn'] }}" class="w-auto p-0">{{ number_format($transaction['coins'], 0) }} <img class="currency-ico" src="/images/{{ $transaction['currency'] }}_flat.png"/> ({{ Helper::formatPrice($transaction['usd']) }}</a>
                    @elseif ($transaction['type'] == 'unstake')
                        <span class="bullet h-auto">Unstake</span><a href="/account/{{ $transaction['to_account'] }}" class="w-auto p-0">{{ number_format($transaction['coins'], 0) }} <img class="currency-ico" src="/images/{{ $transaction['currency'] }}_flat.png"/> ({{ Helper::formatPrice($transaction['usd']) }}</a>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</div>
