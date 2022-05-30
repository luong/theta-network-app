<div class="card c2x tfuel-stake-chart chart m-2 h-auto">
    <h6 class="card-header">
        <span class="icon bi bi-graph-down"></span>
        <span class="name ms-1">TFUEL STAKE CHART</span>
    </h6>
    <div class="card-body">
        <div class="container">
            <div class="chart-title text-center">Supply: {{ number_format($networkInfo['tfuel_supply']) }} # Staked: {{ $networkInfo['tfuel_stake_rate'] * 100 }}% ({{ ($networkInfo['tfuel_stake_change_24h'] > 0 ? '+' : '') . Helper::formatNumber($networkInfo['tfuel_stake_change_24h'], 2, 'M') }})</div>
            <canvas id="tfuelStakeChartHolder"></canvas>
        </div>
    </div>
</div>

<script>
    new Chart(
        document.getElementById('tfuelStakeChartHolder'),
        {
            type: 'line',
            data: {
                datasets: [{
                    backgroundColor: 'rgb(255, 99, 132)',
                    borderColor: 'rgb(255, 99, 132)',
                    pointStyle: 'circle',
                    pointRadius: 5,
                    data: @json($tfuelStakeChartData),
                }]
            },
            options: {
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        }
    );
</script>
