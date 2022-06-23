<x-layout title="ThetaNetworkApp" pageName="nft">
    <div class="nft-page">
        <div class="drops">
            <div class="sizer"></div>
            @foreach ($drops as $drop)
                <div class="{{ $drop['class'] }}"><img src="{{ $drop['image'] . '?w=300' }}"/></div>
            @endforeach
        </div>
    </div>

    <script>
        new Masonry( '.drops', {
            itemSelector: '.drop',
            columnWidth: '.sizer',
            percentPosition: true
        });
    </script>
</x-layout>

