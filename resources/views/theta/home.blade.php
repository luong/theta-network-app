<x-layout title="ThetaNetworkApp" pageName="home">
    <div class="contents d-flex flex-row justify-content-center flex-wrap mt-4 homepage">
        @include('elements/bitcoin-card', ['coinInfo' => $coins['BTC']])
        @include('elements/network-info-card')
        @include('elements/theta-card', ['coinInfo' => $coins['THETA']])
        @include('elements/tfuel-card', ['coinInfo' => $coins['TFUEL']])
        @include('elements/tdrop-card', ['coinInfo' => $coins['TDROP']])
        @include('elements/theta-stake-chart-card')
        @include('elements/tfuel-stake-chart-card')
        @include('elements/tfuel-free-supply-chart-card')
        @include('elements/top-transactions-card')
        @include('elements/top-withdrawals-card')
    </div>
</x-layout>

