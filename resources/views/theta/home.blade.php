<x-layout title="ThetaNetworkApp" pageName="home">
    <div class="contents d-flex flex-row justify-content-center flex-wrap mt-4">
        @include('elements/bitcoin-card', ['coinInfo' => $coins['BTC']])
        @include('elements/network-info-card')
        @include('elements/theta-card', ['coinInfo' => $coins['THETA']])
        @include('elements/tfuel-card', ['coinInfo' => $coins['TFUEL']])
        @include('elements/tdrop-card', ['coinInfo' => $coins['TDROP']])
        @include('elements/tfuel-stake-chart-card')
        @include('elements/theta-stake-chart-card')
        @include('elements/top-transactions-card')
    </div>
</x-layout>

