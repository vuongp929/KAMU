<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Thế giới thú bông!">
    <meta name="author" content="ChillFriend">

    <title>@yield('title', 'ChillFriend')</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    {{-- Link các file CSS--}}
    <link href="{{ asset('assets/clients/plugins/bootstrap/css/bootstrap-theme.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/clients/plugins/bootstrap/css/bootstrap-theme.css.map') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/clients/plugins/bootstrap/css/bootstrap-theme.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/clients/plugins/bootstrap/css/bootstrap.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/clients/plugins/bootstrap/css/bootstrap.css.map') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/clients/plugins/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />

    <script src="{{ asset('assets/clients/bootstrap/bootstrap-touchspin/bootstrap.touchspin.css') }}"></script>
    <script src="{{ asset('assets/clients/bootstrap/bootstrap-touchspin/bootstrap.touchspin.css') }}"></script>
    {{-- corporate --}}
    <link rel="shortcut icon" href="{{ asset('assets/user/images/favicon.ico') }}">
    <link href="{{ asset('assets/clients/corporate/css/themes/blue.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/clients/corporate/css/themes/gray.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/clients/corporate/css/themes/green.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/clients/corporate/css/themes/orange.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/clients/corporate/css/themes/red.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/clients/corporate/css/themes/turquoise.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/clients/corporate/css/custom.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/clients/corporate/css/style-responsive.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/clients/corporate/css/style.css') }}" rel="stylesheet" type="text/css">
    {{-- pages --}}
    <link href="{{ asset('assets/clients/pages/css/style-header.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/clients/pages/css/animate.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/clients/pages/css/components.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/clients/pages/css/gallery.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/clients/pages/css/portfolio.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/clients/pages/css/slider.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/clients/pages/css/style-shop.css') }}" rel="stylesheet" type="text/css">
    
    {{-- CSS riêng cho từng trang --}}
    @yield('CSS')
</head>

<body>

    {{-- Header --}}
    @include('clients.layouts.header')


    {{-- Nội dung chính --}}
    <div class="main-content">
        <div class="container">
            @yield('content')
        </div>
    </div>

    {{-- Pre-footer (nếu có) --}}
    @include('clients.layouts.pre-footer')

    {{-- Footer --}}
    @include('clients.layouts.footer')

    {{-- Các script dùng chung --}}
    <script src="{{ asset('assets/clients/bootstrap/js/bootstrap.js') }}"></script>
    <script src="{{ asset('assets/clients/bootstrap/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/clients/bootstrap/js/npm.js') }}"></script>

    <script src="{{ asset('assets/clients/bootstrap/bootstrap-touchspin/bootstrap.touchspin.js') }}"></script>
    <script src="{{ asset('assets/clients/bootstrap/bootstrap-touchspin/bootstrap.touchspin.min.js') }}"></script>




    <script src="{{ asset('assets/clients/corporate/scripts/back-to-top.js') }}"></script>
    <script src="{{ asset('assets/clients/corporate/scripts/layout.js') }}"></script>

    <script src="{{ asset('assets/clients/pages/scripts/bs-carousel.js') }}"></script>
    <script src="{{ asset('assets/clients/pages/scripts/size-price.js') }}"></script>
    <script src="{{ asset('assets/clients/pages/scripts/checkout.js') }}"></script>
    <script src="{{ asset('assets/clients/pages/scripts/contact-us.js') }}"></script>
    <script src="{{ asset('assets/clients/pages/scripts/portfolio.js') }}"></script>





    {{-- Script riêng cho từng trang --}}
    @yield('JS')
</body>

</html>
