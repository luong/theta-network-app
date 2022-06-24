<div class="card c1x top-withdrawals m-2">
    <h6 class="card-header">
        <span class="icon bi bi-brightness-high"></span>
        <span class="name ms-1">UNSTAKINGS</span>
    </h6>
    <div class="card-body scrollable">
        <div class="container">
            @foreach ($topWithdrawals as $stake)
                <div class="row">
                    <span class="bullet">{{ date('Y-m-d', strtotime($stake['returned_at'])) }}</span> <x-currency type="{{ $stake['currency'] }}"/> <a href="/account/{{ $stake['source'] }}" class="w-auto ps-1">{{ number_format($stake['coins'], 0) }} ({{ Helper::formatPrice($stake['usd']) }})</a>
                </div>
            @endforeach
        </div>
    </div>
</div>
