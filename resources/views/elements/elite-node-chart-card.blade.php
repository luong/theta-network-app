<div class="card c2x elite-node-chart chart m-2 h-auto">
    <h6 class="card-header">
        <a href="/chart/elite-node"><span class="icon bi bi-graph-down"></span></a>
        <span class="name ms-1">ELITE NODE CHART</span>
    </h6>
    <div class="card-body">
        <div class="container">
            <canvas id="eliteNodeChartHolder"></canvas>
        </div>
    </div>
</div>

<script>
    new Chart(
        document.getElementById('eliteNodeChartHolder'),
        {
            type: 'line',
            data: {
                datasets: [{
                    backgroundColor: 'rgb(255, 99, 132)',
                    borderColor: '#3080d0',
                    borderWidth: 1.5,
                    radius: 0,
                    data: @json($eliteNodeChartData),
                }]
            },
            options: {
                responsive: true,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        }
    );
</script>
