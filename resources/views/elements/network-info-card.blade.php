<div class="card c1x network-info m-2">
    <h6 class="card-header">
        <img class="img" src="/images/theta2.png" height="30"/>
        <span class="name ms-1">THETA NETWORK</span>
    </h6>
    <div class="card-body">
        <div class="container">
            <div class="row">
                <div class="col">TVL</div>
                <div class="col">
                    {{ Helper::formatPrice($networkInfo['tvl_value'], 2, 'M') }}
                    <span class="changes {{ $networkInfo['tvl_change_24h'] >= 0 ? 'up' : 'down' }}">({{ ($networkInfo['tvl_change_24h'] > 0 ? '+' : '') . round($networkInfo['tvl_change_24h'] * 100, 2) }}%)</span>
                </div>
            </div>
            <div class="row">
                <div class="col">THETA Price</div>
                <div class="col">
                    {{ Helper::formatPrice($coins['THETA']['price']) }}
                    <span class="changes {{ $coins['THETA']['price_change_24h'] >= 0 ? 'up' : 'down' }}">({{ ($coins['THETA']['price_change_24h'] > 0 ? '+' : '') . round($coins['THETA']['price_change_24h'], 2) }}%)</span>
                </div>
            </div>
            <div class="row">
                <div class="col">TFUEL Price</div>
                <div class="col">
                    {{ Helper::formatPrice($coins['TFUEL']['price']) }}
                    <span class="changes {{ $coins['TFUEL']['price_change_24h'] >= 0 ? 'up' : 'down' }}">({{ ($coins['TFUEL']['price_change_24h'] > 0 ? '+' : '') . round($coins['TFUEL']['price_change_24h'], 2) }}%)</span>
                </div>
            </div>
            <div class="row">
                <div class="col">TDROP Price</div>
                <div class="col">
                    {{ Helper::formatPrice($coins['TDROP']['price']) }}
                    <span class="changes {{ $coins['TDROP']['price_change_24h'] >= 0 ? 'up' : 'down' }}">({{ ($coins['TDROP']['price_change_24h'] > 0 ? '+' : '') . round($coins['TDROP']['price_change_24h'], 2) }}%)</span>
                </div>
            </div>
            <div class="row">
                <div class="col">THETA / TFUEL</div>
                <div class="col">{{ round($networkInfo['theta_price'] / $networkInfo['tfuel_price'], 1) }}</div>
            </div>
            <div class="row">
                <div class="col">Validators</div>
                <div class="col">{{ $networkInfo['validators'] }}</div>
            </div>
            <div class="row">
                <div class="col">Elite Nodes</div>
                <div class="col">{{ number_format($networkInfo['elite_nodes']) }}</div>
            </div>
            <div class="row">
                <div class="col">Guardian Nodes</div>
                <div class="col">{{ number_format($networkInfo['guardian_nodes']) }}</div>
            </div>
            <div class="row">
                <div class="col">Onchain Wallets</div>
                <div class="col">{{ number_format($networkInfo['onchain_wallets']) }}</div>
            </div>
        </div>
    </div>
</div>
