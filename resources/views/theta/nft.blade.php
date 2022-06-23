<x-layout title="ThetaNetworkApp" pageName="nft">
    <div class="nft-page">
        <div class="drops">
            <div class="sizer"></div>
            <div class="drop drop3 host d-flex align-items-center justify-content-center">
                <div class="card ">
                    <h6 class="card-header">
                        <span class="name ms-1">THETA DROP STATISTICS</span>
                    </h6>
                    <div class="card-body">
                        This is some text within a card body.
                    </div>
                </div>
            </div>
            @foreach ($drops as $drop)
                <div class="{{ $drop['class'] }}" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="custom-tooltip" title="{{ $drop['name'] }}"><a href="{{ \App\Helpers\Helper::makeDropContentURL($drop['type']) }}" target="_blank"><img src="{{ $drop['image'] . '?w=200' }}"/></a></div>
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

