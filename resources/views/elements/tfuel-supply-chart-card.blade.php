<div class="card c2x tfuel-supply-chart m-2 h-auto">
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
                    borderColor: 'rgb(255, 99, 132)',
                    pointStyle: 'circle',
                    pointRadius: 5,
                    data: @json($tfuelSupplyChartData),
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