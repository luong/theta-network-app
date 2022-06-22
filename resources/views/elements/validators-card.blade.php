<div class="card c1x validators m-2">
    <h6 class="card-header">
        <span class="icon bi bi-brightness-high"></span>
        <span class="name ms-1">VALIDATORS ({{ count($validators) }})</span>
    </h6>
    <div class="card-body scrollable">
        <div class="container">
            @foreach ($validators as $holder => $props)
                <div class="row">
                    <span class="bullet">{{ $accounts[$holder]['name'] }}</span><a href="/account/{{ $holder }}" class="p-0">{{ Helper::formatNumber($props['coins'], 0, 'K') }}</a> <img class="currency-ico" src="/images/theta_flat.png"/>
                </div>
            @endforeach
        </div>
    </div>
</div>
