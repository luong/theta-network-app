<div class="card c2x theta-drop-sales-chart chart m-2 h-auto">
    <h6 class="card-header">
        <span class="icon bi bi-graph-down"></span>
        <span class="name ms-1">TRANSACTIONS CHART</span>
    </h6>
    <div class="card-body">
        <div class="container">
            <canvas id="transactionsChartHolder"></canvas>
        </div>
    </div>
</div>

<script>
    const chainData = @json($chainData);
    const newChainData = chainData.filter(function (each) {
        return each.transactions_24h > 0;
    });

    Chart.defaults.font.size = 15;
    new Chart(
        document.getElementById('transactionsChartHolder'),
        {
            type: 'line',
            data: {
                datasets: [{
                    backgroundColor: 'rgb(255, 99, 132)',
                    borderColor: '#3080d0',
                    borderWidth: 1.5,
                    radius: 0,
                    data: newChainData,
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
                    yAxisKey: 'transactions_24h'
                },
                scales: {
                    x: {
                        type: 'time',
                        time: {
                            unit: 'month'
                        }
                    }
                }
            }
        }
    );
</script>
