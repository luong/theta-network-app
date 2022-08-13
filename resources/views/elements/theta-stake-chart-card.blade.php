<div class="card c2x theta-stake-chart chart m-2 h-auto">
    <h6 class="card-header">
        <a href="/chart/theta-stake"><span class="icon bi bi-graph-down"></span></a>
        <span class="name ms-1">THETA STAKE CHART</span>
    </h6>
    <div class="card-body">
        <div class="container">
            <div class="chart-title text-center">Supply: {{ Helper::formatNumber($networkInfo['theta_supply'], 0, 'B') }} # Staked: {{ $networkInfo['theta_stake_rate'] * 100 }}% ({{ ($networkInfo['theta_stake_change_24h'] > 0 ? '+' : '') . Helper::formatNumber($networkInfo['theta_stake_change_24h'], 2, 'M') }})</div>
            <canvas id="thetaStakeChartHolder"></canvas>
        </div>
    </div>
</div>

<script>
    new Chart(
        document.getElementById('thetaStakeChartHolder'),
        {
            type: 'line',
            data: {
                datasets: [{
                    backgroundColor: 'rgb(255, 99, 132)',
                    borderColor: '#3080d0',
                    borderWidth: 1.5,
                    radius: 0,
                    data: @json($thetaStakeChartData),
                }]
            },
            options: {
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        ticks: {
                            callback: function(value, index, ticks) {
                                return (value / 1000000).toFixed(2) + 'M';
                            }
                        }
                    }
                }
            }
        }
    );
</script>
