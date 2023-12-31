<x-layout title="Home" pageName="home">
    <div class="contents d-flex flex-row justify-content-center flex-wrap mt-4 homepage">
        @include('elements/bitcoin-card', ['coinInfo' => $coins['BTC']])
        @include('elements/network-info-card')
        @include('elements/theta-card', ['coinInfo' => $coins['THETA']])
        @include('elements/tfuel-card', ['coinInfo' => $coins['TFUEL']])
        @include('elements/tdrop-card', ['coinInfo' => $coins['TDROP']])
        @include('elements/daily-adoption-card')
        @include('elements/top-transactions-card')
        @include('elements/stakings-24h-card')
        @include('elements/unstakings-24h-card')
        @include('elements/unstakings-card')
        @include('elements/validators-card')
    </div>
</x-layout>

