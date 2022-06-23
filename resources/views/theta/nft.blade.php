<x-layout title="ThetaNetworkApp" pageName="nft">
    <div class="nft-page">
        <div class="drops">
            <div class="sizer"></div>
            <div class="drop drop3 host d-flex align-items-center justify-content-center col col-12">
                <div class="card ">
                    <h6 class="card-header">
                        <span class="name ms-1">THETA DROP 24H</span>
                    </h6>
                    <div class="card-body">
                        <div class="container">
                            <div class="row">
                                <div class="col col-8">[{{ $networkInfo['drop_24h']['times_usd'] }}] Stablecoin Sales</div>
                                <div class="col col-4">{{ '$' . number_format($networkInfo['drop_24h']['total_usd'], 0) }}</div>
                            </div>
                            <div class="row">
                                <div class="col col-7">[{{ $networkInfo['drop_24h']['times_tfuel'] }}] Tfuel Sales</div>
                                <div class="col col-5">{{ number_format($networkInfo['drop_24h']['total_tfuel'], 0) }} <img class="currency-ico" src="/images/tfuel_flat.png"/></div>
                            </div>
                            <div class="row">
                                <div class="col col-8">[{{ $networkInfo['drop_24h']['times'] }}] Total Sales</div>
                                <div class="col col-4">{{ '$' . number_format($networkInfo['drop_24h']['total'], 0) }} </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @foreach ($drops as $drop)
                <div class="{{ $drop['class'] }}" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="custom-tooltip" title="{{ $drop['name'] }}"><a href="{{ \App\Helpers\Helper::makeDropContentURL($drop['type']) }}" target="_blank"><img class="img" src="{{ $drop['image'] . '?w=200' }}"/></a></div>
            @endforeach
        </div>
    </div>

    <script>
        imagesLoaded('.drops', function() {
            new Masonry( '.drops', {
                itemSelector: '.drop',
                columnWidth: '.sizer',
                percentPosition: true
            });
        });

        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))
    </script>
</x-layout>

