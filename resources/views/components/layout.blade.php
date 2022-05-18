@props(['title' => 'Theta Network App', 'pageName' => ''])
<!doctype html>
<html lang="en">
<head>
    <title>{{ $title }}</title>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <link rel="icon" href="{{ asset('images/logo.png') }}" type="image/png">
    <link rel="stylesheet" href="{{ mix('/css/app.css') }}">

    <script src="{{ mix('/js/app.js') }}"></script>

    @stack('scripts')
</head>
<body>

<div class="container mt-3 user-layout">
    <div class="row">
        <div class="user-nav-bar col-2 col-md-3">
            <div class="position-fixed h-100">
                <a href="/"><img class="logo mx-4" src="/images/logo.png" width="40"/></a>
                <nav class="nav d-flex flex-column mt-3">
                    <a class="nav-link fs-5 text-body rounded-pill px-4 py-2 @if ($pageName == 'home') active @endif"  href="/"><span class="bi-house-door"></span> <span class="d-none d-md-inline ms-2">Home</span></a>
                    <a class="nav-link fs-5 text-body rounded-pill px-4 py-2 @if ($pageName == 'login') active @endif" href="/login"><span class="bi-brightness-high"></span> <span class="d-none d-md-inline ms-2">Login</span></a>
                    <a class="nav-link fs-5 text-body rounded-pill px-4 py-2 @if ($pageName == 'register') active @endif" href="/register"><span class="bi-person-plus"></span> <span class="d-none d-md-inline ms-2">Register</span></a>
                </nav>
            </div>
        </div>
        <div class="col-10 col-md-7 px-2">{{ $slot }}</div>
        <div class="col col-md-2"></div>
    </div>
</div>

</body>
</html>
