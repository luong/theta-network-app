<div class="card c1x top-transactions m-2">
    <h6 class="card-header">
        <span class="icon bi bi-patch-minus"></span>
        <span class="name ms-1">UNSTAKINGS (<x-currency type="theta" top="11"/>{{ Helper::formatNumber($unstakings['theta'], 2, 'M') }} <x-currency type="tfuel" top="11"/>{{ Helper::formatNumber($unstakings['tfuel'], 2, 'M') }})</span>
    </h6>
    <div class="card-body scrollable">
        <div class="container">
            @foreach ($unstakings['list'] as $stake)
                @php
                    $source = $stake['source'];
                    if (isset($accounts[$stake['source']])) {
                        $source = $accounts[$stake['source']]['name'];
                    }
                @endphp
                <div class="row">
                    <a class="bullet h-auto text-truncate me-0" href="/account/{{ $stake['source'] }}">{{ $source }}</a> <span class="bi bi-caret-right w-auto p-0 m-0"></span> <span class="bullet h-auto text-truncate">{{ $stake['returned_at'] ? date('M-d', strtotime($stake['returned_at'])) : '#' }}</span> <span class="ico-detail p-0 m-0 h-auto w-auto"><x-currency type="{{ $stake['currency'] }}"/> <a class="p-0" href="/account/{{ $stake['source'] }}" class="w-auto ps-1 pe-1">{{ Helper::formatNumber($stake['coins'], 2, 'auto') }} ({{ Helper::formatPrice($stake['usd'], 2, 'auto') }})</a></span>
                </div>
            @endforeach
        </div>
    </div>
</div>
