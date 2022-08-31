<div class="card c2x theta-drop-sales-chart chart m-2 h-auto">
    <h6 class="card-header">
        <span class="icon bi bi-graph-down"></span>
        <span class="name ms-1">THETA DROP SALES CHART</span>
    </h6>
    <div class="card-body">
        <div class="container">
            <canvas id="thetaDropSalesChartHolder"></canvas>
        </div>
    </div>
</div>

<script>
    Chart.defaults.font.size = 15;
    new Chart(
        document.getElementById('thetaDropSalesChartHolder'),
        {
            type: 'line',
            data: {
                datasets: [{
                    backgroundColor: 'rgb(255, 99, 132)',
                    borderColor: '#3080d0',
                    borderWidth: 1.5,
                    radius: 0,
                    data: @json($thetaDropSalesChartData),
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
                        displayColors: false,
                        callbacks: {
                            label: function(context) {
                                return ['Total Sales: $' + context.raw.drop.total.toLocaleString(), 'No. Transfers: ' + context.raw.drop.times];
                            }
                        }
                    }
                },
                parsing: {
                    xAxisKey: 'date',
                    yAxisKey: 'drop.total'
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
                                return '$' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        }
    );
</script>
