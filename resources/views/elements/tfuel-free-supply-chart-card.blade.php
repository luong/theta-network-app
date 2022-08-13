<div class="card c2x tfuel-supply-chart chart m-2 h-auto">
    <h6 class="card-header">
        <a href="/chart/tfuel-free-supply"><span class="icon bi bi-graph-down"></span></a>
        <span class="name ms-1">TFUEL FREE SUPPLY CHART</span>
    </h6>
    <div class="card-body">
        <div class="container">
            <div class="chart-title text-center">
                Free Supply = Circulating Supply - Stakes
            </div>
            <canvas id="tfuelFreeSupplyChartHolder"></canvas>
        </div>
    </div>
</div>

<script>
    new Chart(
        document.getElementById('tfuelFreeSupplyChartHolder'),
        {
            type: 'line',
            data: {
                datasets: [{
                    backgroundColor: 'rgb(255, 99, 132)',
                    borderColor: '#3080d0',
                    borderWidth: 1.5,
                    radius: 0,
                    data: @json($tfuelFreeSupplyChartData),
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
                                return (value / 1000000000).toFixed(3) + 'B';
                            }
                        }
                    }
                }
            }
        }
    );
</script>
