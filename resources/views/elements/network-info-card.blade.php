<div class="card c1x network-info m-2">
    <h6 class="card-header">
        <img class="img" src="/images/theta2.png" height="30"/>
        <span class="name ms-1">THETA NETWORK</span>
    </h6>
    <div class="card-body">
        <div class="container">
            <div class="row">
                <div class="col">THETA / TFUEL</div>
                <div class="col">{{ round($networkInfo['theta_price'] / $networkInfo['tfuel_price'], 1) }}</div>
            </div>
            <div class="row">
                <div class="col">Validators</div>
                <div class="col">{{ $networkInfo['validators'] }}</div>
            </div>
            <div class="row">
                <div class="col">Edge Nodes</div>
                <div class="col">{{ number_format($networkInfo['edge_nodes']) }}</div>
            </div>
            <div class="row">
                <div class="col">Guardian Nodes</div>
                <div class="col">{{ number_format($networkInfo['guardian_nodes']) }}</div>
            </div>
            <div class="row">
                <div class="col">Onchain Wallets</div>
                <div class="col">{{ number_format($networkInfo['onchain_wallets']) }}</div>
            </div>
            <div class="row">
                <div class="col">Active Wallets</div>
                <div class="col">{{ number_format($networkInfo['active_wallets']) }}</div>
            </div>
            <div class="row">
                <div class="col">THETA Stakes</div>
                <div class="col">{{ number_format($networkInfo['theta_stake_rate'] * 100, 2) }}%</div>
            </div>
            <div class="row">
                <div class="col"></div>
                <div class="col">[{{ number_format($networkInfo['theta_stake_nodes']) }} nodes]</div>
            </div>
            <div class="row">
                <div class="col">TFUEL Stakes</div>
                <div class="col">{{ number_format($networkInfo['tfuel_stake_rate'] * 100, 2) }}%</div>
            </div>
            <div class="row">
                <div class="col"></div>
                <div class="col">[{{ number_format($networkInfo['tfuel_stake_nodes']) }} nodes]</div>
            </div>
        </div>
    </div>
</div>
