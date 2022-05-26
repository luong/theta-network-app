<x-layout title="ThetaNetworkApp - Homepage" pageName="home">
    <div class="contents d-flex flex-row justify-content-center flex-wrap mt-4">
        @include('elements/coins-card')
        @include('elements/network-info-card')
        @include('elements/top-transactions-card')
        @include('elements/tfuel-supply-chart-card')
    </div>
</x-layout>

