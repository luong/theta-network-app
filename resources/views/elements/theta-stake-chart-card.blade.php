<div class="card c2x theta-stake-chart chart m-2 h-auto">
    <h6 class="card-header">
        <span class="icon bi bi-graph-down"></span>
        <span class="name ms-1">THETA STAKE CHART</span>
    </h6>
    <div class="card-body">
        <div class="container">
            <div class="chart-title text-center">Current supply: {{ number_format($networkInfo['theta_supply']) }} # Staked: {{ $networkInfo['theta_stake_rate'] * 100 }}%</div>
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
                    borderColor: 'rgb(255, 99, 132)',
                    pointStyle: 'circle',
                    pointRadius: 5,
                    data: @json($thetaStakeChartData),
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
