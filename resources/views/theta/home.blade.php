<x-layout title="ThetaNetworkApp" pageName="home">
    <div class="contents d-flex flex-row justify-content-center flex-wrap mt-4">
        @include('elements/coins-card')
        @include('elements/network-info-card')
        @include('elements/tfuel-supply-chart-card')
        @include('elements/tfuel-stake-chart-card')
        @include('elements/theta-stake-chart-card')
        @include('elements/top-transactions-card')
    </div>
</x-layout>

