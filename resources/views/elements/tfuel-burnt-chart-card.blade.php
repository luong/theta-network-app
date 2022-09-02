<div class="card c2x tfuel-burnt-chart chart m-2 h-auto">
    <h6 class="card-header">
        <a href="/chart/tfuel-burnt"><span class="icon bi bi-graph-down"></span></a>
        <span class="name ms-1">TFUEL BURNT CHART</span>
    </h6>
    <div class="card-body">
        <div class="container">
            <canvas id="tfuelBurntChartHolder"></canvas>
        </div>
    </div>
</div>

<script>
    new Chart(
        document.getElementById('tfuelBurntChartHolder'),
        {
            type: 'line',
            data: {
                datasets: [{
                    backgroundColor: 'rgb(255, 99, 132)',
                    borderColor: '#3080d0',
                    borderWidth: 1.5,
                    radius: 0,
                    data: @json($tfuelBurntChartData),
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
                    },
                    tooltip: {
                        displayColors: false
                    }
                },
                scales: {
                    x: {
                        type: 'time',
                        time: {
                            unit: 'month',
                            tooltipFormat: 'YYYY-MM-DD'
                        }
                    }
                }
            }
        }
    );
</script>
