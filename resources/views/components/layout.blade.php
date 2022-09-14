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
            <a class="top-logo navbar-brand fs-3 text-secondary" href="/">
                <img src="{{ asset('images/theta.png') }}" width="36" style="vertical-align: top; margin-top: 0px;"/>
                <span style="">ThetaPizza</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
                <div class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link text-secondar fs-5" href="/accounts"><span class="bi bi-snapchat"></span> Accounts</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-secondar fs-5" href="/transactions"><span class="bi bi-layers"></span> Transactions</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-secondar fs-5" href="/volumes"><span class="bi bi-badge-vo"></span> Volumes</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-secondar fs-5" href="#" data-bs-toggle="dropdown" aria-expanded="false"><span class="bi bi-bar-chart"></span> Charts</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="/chart/tfuel-stake">Tfuel Stakes</a></li>
                            <li><a class="dropdown-item" href="/chart/elite-node">Elite Nodes</a></li>
                            <li><a class="dropdown-item" href="/chart/tfuel-supply">Tfuel Supply</a></li>
                            <li><a class="dropdown-item" href="/chart/tfuel-burnt">Tfuel Burnt</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="/chart/theta-stake">Theta Stakes</a></li>
                            <li><a class="dropdown-item" href="/chart/gold-ratio">Gold Ratio</a></li>
                            <li><a class="dropdown-item" href="/chart/theta-drop-sales">Theta Drop Sales</a></li>
                            <li><a class="dropdown-item" href="/chart/transactions">Transactions</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a id="contactLink" class="nav-link text-secondary fs-5" href="https://twitter.com/ThetaPizza" target="_blank"><span class="bi bi-twitter"></span> Contact</a>
                    </li>
                </div>
                <form class="d-flex" role="search" action="/search">
                    <div class="col-12">
                        <input class="form-control me-md-4" type="search" name="q" placeholder="Search by address or txn"/>
                    </div>
                </form>
            </div>
        </div>
    </nav>

    @include('elements/donate-model')

    <div class="">{{ $slot }}</div>

    <footer class="mt-5 mb-2">
        <div class="text-center">
            ** Prices updated every 5 minutes. Daily data updated at 00:30 UTC.
            <br/>
            ** <a id="donateLink" href="#" data-bs-toggle="modal" data-bs-target="#donateModel">Donate for hosting the site ($40 / month)</a>. If you want to add any feature, please feel free to <a href="https://twitter.com/ThetaPizza" target="_blank">let me know</a>.
        </div>
    </footer>

</div>

@if (session('message'))
    <div class="page-message fixed-bottom {{ session('message')[0] }}">{{ session('message')[1] }}</div>
    <script>
        $('.page-message').delay(2500).fadeOut('slow')
    </script>
@endif

<script>
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))
</script>

</body>
</html>
