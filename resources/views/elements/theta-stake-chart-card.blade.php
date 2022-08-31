<div class="card c2x theta-stake-chart chart m-2 h-auto">
    <h6 class="card-header">
        <a href="/chart/theta-stake"><span class="icon bi bi-graph-down"></span></a>
        <span class="name ms-1">THETA STAKE CHART</span>
    </h6>
    <div class="card-body">
        <div class="container">
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
                    data: @json($thetaData),
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
                parsing: {
                    xAxisKey: 'date',
                    yAxisKey: 'total_stakes'
                },
                scales: {
                    x: {
                        type: 'time',
                        time: {
                            unit: 'month',
                            tooltipFormat: 'YYYY-MM-DD'
                        }
                    },
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
