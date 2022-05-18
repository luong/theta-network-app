<x-layout title="ThetaNetworkApp - Homepage" pageName="home">
    <script>
    </script>

    <div class="contents d-flex flex-row justify-content-center flex-wrap mt-4">
        @foreach ($coins as $coin)
            <div class="card coin m-2">
                <h5 class="card-header">
                    <img class="img" src="{{ $coin['image'] }}" height="30"/>
                    <span class="name ms-1">{{ $coin['name'] }}</span>
                </h5>
                <div class="card-body">
                    <div class="container">
                        <div class="row">
                            <div class="col">Price</div>
                            <div class="col">${{ Helper::formatPrice($coin['price']) }}</div>
                        </div>
                        <div class="row">
                            <div class="col">24 Hour Vol</div>
                            <div class="col">${{ number_format($coin['volume_24h'], 0) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</x-layout>
