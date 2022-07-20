@props(['title' => 'Theta Pizza', 'pageName' => ''])
<!doctype html>
<html lang="en">
<head>
    <title>{{ $title }}</title>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <link rel="icon" href="{{ asset('images/theta.png') }}" type="image/png">
    <link rel="stylesheet" href="{{ mix('/css/admin.css') }}">

    <script>
        var pageName = '{{ $pageName }}';
    </script>

    <script src="{{ mix('/js/app.js') }}"></script>

    @stack('scripts')
</head>
<body>

<div class="admin-layout">

    <header class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0 shadow">
        <a class="navbar-brand col-md-3 col-lg-2 me-0 px-3" href="{{ route('admin.home') }}">ThetaPizza</a>
        <button class="navbar-toggler position-absolute d-md-none collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="navbar-nav">
            <div class="nav-item text-nowrap">
            </div>
        </div>
    </header>

    <div class="container-fluid">
        <div class="row">
            <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
                <div class="position-sticky pt-3">

                    @auth('admin')
                        <ul class="nav flex-column">
                            <li class="nav-item">
                                <a class="nav-link" aria-current="page" href="{{ route('admin.home') }}" page="home"><span class="bi-house-door"></span> Home</a>
                            </li>
                        </ul>

                        <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
                            <span>Networking</span>
                        </h6>
                        <ul class="nav flex-column mb-2">
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.validators') }}" page="validators"><span class="bi-at"></span> Validators</a>
                                <a class="nav-link" href="{{ route('admin.stakes') }}" page="stakes"><span class="bi-save"></span> Stakes</a>
                                <a class="nav-link" href="{{ route('admin.accounts') }}" page="accounts"><span class="bi-diamond"></span> Accounts</a>
                                <a class="nav-link" href="{{ route('admin.transactions') }}" page="top_transactions"><span class="bi-shop"></span> Transactions</a>
                                <a class="nav-link" href="{{ route('admin.topActivists') }}" page="top_activists"><span class="bi-broadcast"></span> Top Activists</a>
                            </li>
                        </ul>

                        <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
                            <span>Administration</span>
                        </h6>
                        <ul class="nav flex-column mb-2">
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.admins') }}" page="admins"><span class="bi-person"></span> Admins</a>
                                <a class="nav-link" href="{{ route('admin.logs') }}" page="logs"><span class="bi-bug"></span> Logs</a>
                            </li>
                        </ul>

                        <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
                            <span>Hi, {{ auth('admin')->user()->name }}</span>
                        </h6>
                        <ul class="nav flex-column mb-2">
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.logout') }}"><span class="bi-slash-circle"></span> Logout</a>
                                <span class="nav-link"><span class="bi-clock"></span> {{ 'D' . date('d H:i:s') }}</span>
                            </li>
                        </ul>
                    @endauth

                </div>
            </nav>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2 page-header">{{ $header ?? '' }}</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        {{ $menus ?? '' }}
                    </div>
                </div>

                <div class="page-content">{{ $slot }}</div>
            </main>

        </div>
    </div>

</div>

<script>
    $(function() {
        let selectedPageObj = $('#sidebarMenu a.nav-link[page=' + pageName + ']');
        if (pageName && selectedPageObj) {
            $('#sidebarMenu a.nav-link').removeClass('active');
            selectedPageObj.addClass('active');
        }
    });
</script>

</body>
</html>
