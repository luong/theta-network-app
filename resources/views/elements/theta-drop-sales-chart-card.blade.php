<div class="card c2x theta-drop-sales-chart chart m-2 h-auto">
    <h6 class="card-header">
        <span class="icon bi bi-graph-down"></span>
        <span class="name ms-1">THETA DROP SALES CHART</span>
    </h6>
    <div class="card-body">
        <div class="container">
            <div class="chart-title text-center">
            </div>
            <canvas id="thetaDropSalesChartHolder"></canvas>
        </div>
    </div>
</div>

<script>
    new Chart(
        document.getElementById('thetaDropSalesChartHolder'),
        {
            type: 'line',
            data: {
                datasets: [{
                    backgroundColor: 'rgb(255, 99, 132)',
                    borderColor: 'rgb(255, 99, 132)',
                    pointStyle: 'circle',
                    pointRadius: 2,
                    data: @json($thetaDropSalesChartData),
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
