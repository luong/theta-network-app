<div class="card c1x coin daily-adaption m-2">
    <h6 class="card-header">
        <span class="icon bi bi-brightness-high"></span>
        <span class="name ms-1">Daily Adoption</span>
    </h6>
    <div class="card-body">
        <div class="container">
            <div class="row">
                <div class="col">Block Height</div>
                <div class="col">{{ $networkInfo['block_height'] }}</div>
            </div>
            <div class="row">
                <div class="col">Transactions</div>
                <div class="col">{{ number_format($networkInfo['transactions_24h']) }}</div>
            </div>
            <div class="row">
                <div class="col">Tfuel Supply</div>
                <div class="col">{{ number_format($networkInfo['tfuel_total_supply_change_24h']) }}</div>
            </div>
            <div class="row">
                <div class="col">Tfuel Burnt</div>
                <div class="col">{{ number_format($networkInfo['tfuel_total_burnt_change_24h']) }}</div>
            </div>
            <div class="row">
                <div class="col">Tfuel Net Supply</div>
                <div class="col">{{ ($networkInfo['tfuel_supply_change_24h'] > 0 ? '+' : '') . number_format($networkInfo['tfuel_supply_change_24h']) }}</div>
            </div>
            <div class="row">
                <div class="col">Tfuel Staking</div>
                <div class="col">{{ ($networkInfo['tfuel_stake_change_24h'] > 0 ? '+' : '') . number_format($networkInfo['tfuel_stake_change_24h']) }}</div>
            </div>
            <div class="row">
                <div class="col">Theta Staking</div>
                <div class="col">{{ ($networkInfo['theta_stake_change_24h'] > 0 ? '+' : '') . number_format($networkInfo['theta_stake_change_24h']) }}</div>
            </div>
            <div class="row">
                <div class="col">T. Drop Trans</div>
                <div class="col">{{ Helper::formatNumber($networkInfo['drop_times']) . ' (' . ($networkInfo['drop_times_change_24h'] >= 0 ? '+' : '') . Helper::formatNumber($networkInfo['drop_times_change_24h'] * 100, 2) . '%)' }}</div>
            </div>
            <div class="row">
                <div class="col">T. Drop Sales</div>
                <div class="col">{{ Helper::formatPrice($networkInfo['drop_sales'], 2) . ' (' . ($networkInfo['drop_sales_change_24h'] >= 0 ? '+' : '') . Helper::formatNumber($networkInfo['drop_sales_change_24h'] * 100, 2) . '%)' }}</div>
            </div>
        </div>
    </div>
</div>
