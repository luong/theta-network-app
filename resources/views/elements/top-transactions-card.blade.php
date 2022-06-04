<div class="card c2x top-transactions m-2">
    <h6 class="card-header">
        <span class="icon bi bi-brightness-high"></span>
        <span class="name ms-1">TOP TRANSACTIONS</span>
    </h6>
    <div class="card-body scrollable">
        <div class="container">
            @foreach ($topTransactions as $hash => $transaction)
                <div class="row">
                    @if ($transaction['type'] == 'transfer')
                        <span class="bullet h-auto">T</span><a href="{{ Helper::makeThetaTransactionURL($hash) }}" target="_blank" class="w-auto p-0">{{ $transaction['amount'] }}</a> <span class="d-none d-lg-inline w-auto p-0 ps-1">transferred on {{ $transaction['date'] }}</span>
                    @elseif ($transaction['type'] == 'stake')
                        <span class="bullet h-auto">S</span><a href="{{ Helper::makeThetaTransactionURL($hash) }}" target="_blank" class="w-auto p-0">{{ $transaction['amount'] }}</a> <span class="d-none d-lg-inline w-auto p-0 ps-1">staked on {{ $transaction['date'] }}</span>
                    @elseif ($transaction['type'] == 'withdraw')
                        <span class="bullet h-auto">W</span><a href="{{ Helper::makeThetaAccountURL($transaction['from']) }}" target="_blank" class="w-auto p-0">{{ $transaction['amount'] }}</a> <span class="d-none d-lg-inline w-auto p-0 ps-1">withdrawn on {{ $transaction['date'] }}</span>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</div>
