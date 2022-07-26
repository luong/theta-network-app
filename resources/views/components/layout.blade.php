@props(['title' => 'Theta Pizza', 'pageName' => ''])
<!doctype html>
<html lang="en">
<head>
    <title>{{ $title }}</title>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="News and analysis of Theta blockchain networking, cryptocurrencies of $theta, $tfuel, $tdrop">
    <meta name="keywords" content="Theta, Tfuel, Tdrop, Theta Drop, Blockchain, Decentralized networking">

    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <link rel="icon" href="{{ asset('images/theta.png') }}" type="image/png">
    <link rel="stylesheet" href="{{ mix('/css/app.css') }}">

    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
                new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
            j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
            'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','GTM-MGL8HGG');</script>
    <!-- End Google Tag Manager -->

    <script src="{{ mix('/js/app.js') }}"></script>

    @stack('scripts')
</head>
<body>

<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-MGL8HGG"
                  height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->

<div class="container-fluid m-0 p-0 user-layout">
    <nav class="navbar navbar-light navbar-expand-lg bg-light" style="background-color:#e3f2fd">
        <div class="container-fluid">
            <a class="navbar-brand fs-3 text-secondary" href="/">
                <img src="{{ asset('images/theta.png') }}" width="36" style="vertical-align: top; margin-top: 3px;"/>
                ThetaPizza
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse float-end" id="navbarNavAltMarkup" style="flex-grow: 0">
                <div class="navbar-nav">
                    <a class="nav-link text-secondary fs-5" href="/whales"><span class="bi bi-snow"></span> Whales</a>
                    <a id="nftLink" class="nav-link text-secondary fs-5" href="/nft"><span class="bi bi-valentine"></span> ThetaDrop</a>
                    <a id="donateLink" class="nav-link text-secondary fs-5" href="#" data-bs-toggle="modal" data-bs-target="#donateModel"><span class="bi bi-currency-bitcoin"></span> Donate</a>
                    <a id="contactLink" class="nav-link text-secondary fs-5" href="https://twitter.com/ThetaPizza" target="_blank"><span class="bi bi-twitter"></span> Contact</a>
                </div>
            </div>
        </div>
    </nav>

    @include('elements/donate-model')

    <div class="">{{ $slot }}</div>

</div>

@if (session('message'))
    <div class="page-message fixed-bottom {{ session('message')[0] }}">{{ session('message')[1] }}</div>
    <script>
        $('.page-message').delay(2500).fadeOut('slow')
    </script>
@endif

</body>
</html>
