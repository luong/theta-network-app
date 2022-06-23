<x-layout title="ThetaNetworkApp" pageName="nft">
    <div class="nft-page">
        <div class="drops">
            <div class="sizer"></div>
            @foreach ($drops as $drop)
                <div class="{{ $drop['class'] }}" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="custom-tooltip" title="{{ $drop['name'] }}"><img src="{{ $drop['image'] . '?w=200' }}"/></div>
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

