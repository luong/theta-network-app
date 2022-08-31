<div class="card c2x tfuel-supply-chart chart m-2 h-auto">
    <h6 class="card-header">
        <span class="icon bi bi-graph-down"></span>
        <span class="name ms-1">TFUEL SUPPLY CHART</span>
    </h6>
    <div class="card-body">
        <div class="container">
            <canvas id="tfuelSupplyChartHolder"></canvas>
        </div>
    </div>
</div>

<script>
    new Chart(
        document.getElementById('tfuelSupplyChartHolder'),
        {
            type: 'line',
            data: {
                datasets: [{
                    backgroundColor: 'rgb(255, 99, 132)',
                    borderColor: '#3080d0',
                    borderWidth: 1.5,
                    radius: 0,
                    data: @json($tfuelData),
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
                    yAxisKey: 'supply'
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
                                return (value / 1000000000).toFixed(3) + 'B';
                            }
                        }
                    }
                }
            }
        }
    );
</script>
