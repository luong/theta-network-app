<x-layout title="ThetaNetworkApp - Homepage" pageName="home">
    <script>
    </script>

    <div class="contents d-flex flex-row justify-content-center flex-wrap mt-4">
        @foreach ($coins as $coin)
            <div class="card c1x coin m-2">
                <h6 class="card-header">
                    <img class="img" src="{{ $coin['image'] }}" height="30"/>
                    <span class="name ms-1">{{ $coin['name'] }}</span>
                </h6>
                <div class="card-body">
                    <div class="container">
                        <div class="row">
                            <div class="col">Price</div>
                            <div class="col">
                                {{ Helper::formatPrice($coin['price']) }}
                                <span class="changes {{ $coin['price_change_24h'] >= 0 ? 'up' : 'down' }}">({{ round($coin['price_change_24h'], 2) }}%)</span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">24 Hour Vol</div>
                            <div class="col">{{ Helper::formatPrice($coin['volume_24h']) }}</div>
                        </div>
                        <div class="row">
                            <div class="col">Market Cap</div>
                            <div class="col">{{ Helper::formatPrice($coin['market_cap'], 0) }}</div>
                        </div>
                        <div class="row">
                            <div class="col">Circulating Supply</div>
                            <div class="col">{{ number_format($coin['circulating_supply']) }}</div>
                        </div>
                        <div class="row">
                            <div class="col">1Y Changes</div>
                            <div class="col">{{ round($coin['price_change_1y'], 2) }}%</div>
                        </div>
                        <div class="row">
                            <div class="col">ATH</div>
                            <div class="col">
                                {{ Helper::formatPrice($coin['ath']) }}
                                <span class="changes">({{ round($coin['price_change_ath'], 2) }}%)</span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">Ranking</div>
                            <div class="col">#{{ $coin['market_cap_rank'] }}</div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

        <div class="card c1x network-info m-2">
            <h6 class="card-header">
                <img class="img" src="/images/theta2.png" height="30"/>
                <span class="name ms-1">THETA NETWORK</span>
            </h6>
            <div class="card-body">
                <div class="container">
                    <div class="row">
                        <div class="col">THETA / TFUEL</div>
                        <div class="col">{{ round($coins['THETA']['price'] / $coins['TFUEL']['price'], 1) }}</div>
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
                        <div class="col">THETA Stakes</div>
                        <div class="col">{{ number_format($networkInfo['theta_stake_rate'] * 100, 2) }}%</div>
                    </div>
                    <div class="row">
                        <div class="col">TFUEL Stakes</div>
                        <div class="col">{{ number_format($networkInfo['tfuel_stake_rate'] * 100, 2) }}%</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card c2x top-transactions m-2">
            <h6 class="card-header">
                <span class="icon bi bi-brightness-high"></span>
                <span class="name ms-1">TOP TRANSACTIONS</span>
            </h6>
            <div class="card-body">
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

    </div>
</x-layout>
