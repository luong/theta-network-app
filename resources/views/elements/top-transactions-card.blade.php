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
                        <span class="bullet h-auto">T</span><a href="/transaction/{{ $transaction['txn'] }}" class="w-auto p-0">{{ number_format($transaction['coins'], 0) . ' ' . $transaction['currency'] . ' (' .  Helper::formatPrice($transaction['usd']) . ')' }}</a>
                    @elseif ($transaction['type'] == 'stake')
                        <span class="bullet h-auto">S</span><a href="/transaction/{{ $transaction['txn'] }}" class="w-auto p-0">{{ number_format($transaction['coins'], 0) . ' ' . $transaction['currency'] . ' (' .  Helper::formatPrice($transaction['usd']) . ')' }}</a>
                    @elseif ($transaction['type'] == 'withdraw')
                        <span class="bullet h-auto">W</span><a href="/account/{{ $transaction['to_account'] }}" class="w-auto p-0">{{ number_format($transaction['coins'], 0) . ' ' . $transaction['currency'] . ' (' .  Helper::formatPrice($transaction['usd']) . ')' }}</a>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</div>
