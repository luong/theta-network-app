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
                        <span class="bi bi-circle w-auto"></span><a href="https://explorer.thetatoken.org/txs/{{ $hash }}" target="_blank" class="w-auto ps-0">{{ $transaction['amount'] }}</a> transferred on {{ $transaction['date'] }}
                    @elseif ($transaction['type'] == 'stake')
                        <span class="bi bi-circle w-auto"></span><a href="https://explorer.thetatoken.org/txs/{{ $hash }}" target="_blank" class="w-auto ps-0">{{ $transaction['amount'] }}</a> staked on {{ $transaction['date'] }}
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</div>
