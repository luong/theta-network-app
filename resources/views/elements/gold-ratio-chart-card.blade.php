<div class="card c2x gold-ratio-chart chart m-2 h-auto">
    <h6 class="card-header">
        <a href="/chart/gold-ratio"><span class="icon bi bi-graph-down"></span></a>
        <span class="name ms-1">GOLD RATIO CHART</span>
    </h6>
    <div class="card-body">
        <div class="container">
            <canvas id="goldRatioChartHolder"></canvas>
        </div>
    </div>
</div>

<script>
    new Chart(
        document.getElementById('goldRatioChartHolder'),
        {
            type: 'line',
            data: {
                datasets: [{
                    backgroundColor: 'rgb(255, 99, 132)',
                    borderColor: 'rgb(255, 99, 132)',
                    pointStyle: 'circle',
                    pointRadius: 1,
                    data: @json($data),
                }]
            },
            options: {
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return ['$theta: ' + context.raw.theta, '$tfuel: ' + context.raw.tfuel, 'ratio: ' + context.raw.ratio];
                            }
                        }
                    }
                },
                parsing: {
                    xAxisKey: 'date',
                    yAxisKey: 'ratio'
                }
            }
        }
    );
</script>
