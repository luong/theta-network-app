<div class="card c1x top-withdrawals m-2">
    <h6 class="card-header">
        <span class="icon bi bi-brightness-high"></span>
        <span class="name ms-1">STAKINGS 24H (<x-currency type="theta" top="11"/>{{ Helper::formatNumber($stakings24H['theta'], 1, 'M') }} <x-currency type="tfuel" top="11"/>{{ Helper::formatNumber($stakings24H['tfuel'], 1, 'M') }})</span>
    </h6>
    <div class="card-body scrollable">
        <div class="container">
            @foreach ($stakings24H['list'] as $transaction)
                <div class="row">
                    <span class="bullet">{{ Str::limit(isset($accounts[$transaction['to_account']]) ? $accounts[$transaction['to_account']]['name'] : $transaction['to_account'], 8, '') }}</span> <x-currency type="{{ $transaction['currency'] }}"/> <a href="/transaction/{{ $transaction['txn'] }}" class="w-auto ps-1">{{ number_format($transaction['coins'], 0) }} ({{ Helper::formatPrice($transaction['usd']) }})</a>
                </div>
            @endforeach
        </div>
    </div>
</div>
