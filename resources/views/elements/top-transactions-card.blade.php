<div class="card c1x top-transactions m-2">
    <h6 class="card-header">
        <span class="icon bi bi-brightness-high"></span>
        <span class="name ms-1">TOP TRANSACTIONS 24H</span>
    </h6>
    <div class="card-body scrollable">
        <div class="container">
            @foreach ($topTransactions as $transaction)
                @php
                    $from = $transaction['from_account'];
                    $to = $transaction['to_account'];
                    if (isset($accounts[$transaction['from_account']])) {
                        $from = $accounts[$transaction['from_account']]['name'];
                    }
                    if (isset($accounts[$transaction['to_account']])) {
                        $to = $accounts[$transaction['to_account']]['name'];
                    }
                    $from = Str::limit($from, 7, '');
                    $to = Str::limit($to, 7, '');
                @endphp
                <div class="row">
                    <a class="bullet h-auto" href="/account/{{ $transaction['from_account'] }}">{{ $from }}</a> <a class="bullet h-auto" href="/account/{{ $transaction['to_account'] }}">{{ $to }}</a> <span class="ico-detail p-0 m-0 h-auto w-auto"><x-currency type="{{ $transaction['currency'] }}"/> <a class="p-0" href="{{ Helper::makeSiteTransactionURL($transaction['txn'], $transaction['currency']) }}" class="w-auto ps-1 pe-1">{{ Helper::formatNumber($transaction['coins'], 2, 'auto') }} ({{ Helper::formatPrice($transaction['usd'], 2, 'auto') }})</a></span>
                </div>
            @endforeach
        </div>
    </div>
</div>
