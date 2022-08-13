<div class="card c2x theta-drop-sales-chart chart m-2 h-auto">
    <h6 class="card-header">
        <span class="icon bi bi-graph-down"></span>
        <span class="name ms-1">THETA DROP SALES CHART</span>
    </h6>
    <div class="card-body">
        <div class="container">
            <div class="chart-title text-center mb-3">
                <div class="card">
                    <div class="card-body p-2">
                        <h6 class="card-title">Stats 24H</h6>
                        <div class="row">
                            <div class="col col-8">[{{ $networkInfo['drop_24h']['times_usd'] }}] Stablecoin Sales</div>
                            <div class="col col-4">{{ '$' . number_format($networkInfo['drop_24h']['total_usd'], 0) }}</div>
                        </div>
                        <div class="row">
                            <div class="col col-7">[{{ $networkInfo['drop_24h']['times_tfuel'] }}] Tfuel Sales</div>
                            <div class="col col-5"><x-currency type="tfuel"/> {{ number_format($networkInfo['drop_24h']['total_tfuel'], 0) }}</div>
                        </div>
                        <div class="row">
                            <div class="col col-8">[{{ $networkInfo['drop_24h']['times'] }}] Total Sales</div>
                            <div class="col col-4">{{ '$' . number_format($networkInfo['drop_24h']['total'], 0) }} </div>
                        </div>
                    </div>
                </div>
            </div>
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
                    }
                }
            }
        }
    );
</script>
