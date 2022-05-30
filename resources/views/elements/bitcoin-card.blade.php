<div class="card c1x coin btc m-2">
    <h6 class="card-header">
        <img class="img" src="{{ $coinInfo['image'] }}" height="30"/>
        <span class="name ms-1">{{ $coinInfo['name'] }}</span>
    </h6>
    <div class="card-body">
        <div class="container">
            <div class="row">
                <div class="col">Ranking</div>
                <div class="col">#{{ $coinInfo['market_cap_rank'] }}</div>
            </div>
            <div class="row">
                <div class="col">Price</div>
                <div class="col">
                    {{ Helper::formatPrice($coinInfo['price']) }}
                    <span class="changes {{ $coinInfo['price_change_24h'] >= 0 ? 'up' : 'down' }}">({{ ($coinInfo['price_change_24h'] >= 0 ? '+' : '') . round($coinInfo['price_change_24h'], 2) }}%)</span>
                </div>
            </div>
            <div class="row">
                <div class="col">24 Hour Vol</div>
                <div class="col">{{ Helper::formatPrice($coinInfo['volume_24h']) }}</div>
            </div>
            <div class="row">
                <div class="col">Market Cap</div>
                <div class="col">{{ Helper::formatPrice($coinInfo['market_cap'], 0) }}</div>
            </div>
            <div class="row">
                <div class="col">Circulating Supply</div>
                <div class="col">{{ number_format($coinInfo['circulating_supply']) }}</div>
            </div>
            <div class="row">
                <div class="col">1Y Changes</div>
                <div class="col">{{ ($coinInfo['price_change_1y'] >= 0 ? '+' : '') . round($coinInfo['price_change_1y'], 2) }}%</div>
            </div>
            <div class="row">
                <div class="col">ATH</div>
                <div class="col">
                    {{ Helper::formatPrice($coinInfo['ath']) }}
                    <span class="changes">({{ ($coinInfo['price_change_ath'] >= 0 ? '+' : '') . round($coinInfo['price_change_ath'], 2) }}%)</span>
                </div>
            </div>
        </div>
    </div>
</div>
