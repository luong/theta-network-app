<div class="card c1x top-transactions validators m-2">
    <h6 class="card-header">
        <span class="icon bi bi-palette"></span>
        <span class="name ms-1">VALIDATORS ({{ count($validators) }})</span>
    </h6>
    <div class="card-body scrollable">
        <div class="container">
            @foreach ($validators as $holder => $props)
                <div class="row">
                    <a class="bullet text-truncate" href="/account/{{ $holder }}">{{ $accounts[$holder]['name'] }}</a> <x-currency type="theta"/> <a href="/account/{{ $holder }}" class="w-auto ps-1">{{ Helper::formatNumber($props['coins'], 2, 'auto') }}</a>
                </div>
            @endforeach
        </div>
    </div>
</div>
