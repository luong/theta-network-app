<div class="card c1x top-withdrawals m-2">
    <h6 class="card-header">
        <span class="icon bi bi-brightness-high"></span>
        <span class="name ms-1">UNSTAKINGS 24H (<x-currency type="theta" top="11"/>{{ Helper::formatNumber($unstakings24H['theta'], 2, 'M') }} <x-currency type="tfuel" top="11"/>{{ Helper::formatNumber($unstakings24H['tfuel'], 2, 'M') }})</span>
    </h6>
    <div class="card-body scrollable">
        <div class="container">
            @foreach ($unstakings24H['list'] as $transaction)
                <div class="row">
                    <span class="bullet">{{ Str::limit(isset($accounts[$transaction['from_account']]) ? $accounts[$transaction['from_account']]['name'] : $transaction['from_account'], 9, '') }}</span> <x-currency type="{{ $transaction['currency'] }}"/> <a href="/transaction/{{ $transaction['txn'] }}" class="w-auto ps-1">{{ number_format($transaction['coins'], 0) }} ({{ Helper::formatPrice($transaction['usd']) }})</a>
                </div>
            @endforeach
        </div>
    </div>
</div>
