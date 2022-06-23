@props(['title' => 'ThetaNetworkApp', 'pageName' => ''])
<!doctype html>
<html lang="en">
<head>
    <title>{{ $title }}</title>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <link rel="icon" href="{{ asset('images/theta.png') }}" type="image/png">
    <link rel="stylesheet" href="{{ mix('/css/app.css') }}">

    <script src="{{ mix('/js/app.js') }}"></script>

    @stack('scripts')
</head>
<body>

<div class="container-fluid m-0 p-0 user-layout">
    <nav class="navbar navbar-light navbar-expand-lg bg-light" style="background-color:#e3f2fd">
        <div class="container-fluid">
            <a class="navbar-brand fs-3 text-secondary" href="/">
                <img src="{{ asset('images/theta.png') }}" width="36" style="vertical-align: top; margin-top: 3px;"/>
                ThetaNetworkApp
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse float-end" id="navbarNavAltMarkup" style="flex-grow: 0">
                <div class="navbar-nav">
                    <a id="nftLink" class="nav-link text-secondary fs-5" href="/nft"><span class="bi bi-valentine"></span> ThetaDrop</a>
                    <a id="donateLink" class="nav-link text-secondary fs-5" href="#" data-bs-toggle="modal" data-bs-target="#donateModel"><span class="bi bi-currency-bitcoin"></span> Donate</a>
                    <a id="contactLink" class="nav-link text-secondary fs-5" href="https://twitter.com/ThetaNetworkApp" target="_blank"><span class="bi bi-twitter"></span> Contact</a>
                </div>
            </div>
        </div>
    </nav>

    @include('elements/donate-model')

    <div class="">{{ $slot }}</div>
</div>

<script>
    mixpanel.track('ViewPage', { page: '{{ $pageName }}' });
    mixpanel.track_links('#donateLink', 'Click Donate');
    mixpanel.track_links('#contactLink', 'Click Contact');
</script>

</body>
</html>
