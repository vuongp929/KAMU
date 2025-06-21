<!doctype html>
<html lang="en" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg"
    data-sidebar-image="none" data-preloader="disable" data-theme="default" data-theme-colors="default">


<!-- Mirrored from themesbrand.com/velzon/html/master/index.html by HTTrack Website Copier/3.x [XR&CO'2014], Tue, 29 Oct 2024 07:29:52 GMT -->

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Premium Multipurpose Admin & Dashboard Template" name="description" />
    <meta content="Themesbrand" name="author" />

    <title>@yield('title') - Admin Dashboard</title>
    {{-- Điền các link CSS dùng chung --}}
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('admins/img/logo.png') }}">

    <!-- Bootstrap Css -->
    <link href="{{ asset('admins/css/bootstrap1.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Main Style Css -->
    <link href="{{ asset('admins/css/style1.css') }}" rel="stylesheet" type="text/css" />
    <!-- Menu Css -->
    <link href="{{ asset('admins/css/metisMenu.css') }}" rel="stylesheet" type="text/css" />
    <!-- Nếu có thêm css custom thì thêm ở đây -->
    @stack('styles')
</head>

<body id="page-top">
    <div id="layout-wrapper">

        @include('admins.partials.siderbar')

        <div class="vertical-overlay"></div>

        <section class="main_content">
            @include('admins.partials.header')
            <div class="main_content_iner">
                @yield('content')
            </div>
            @include('admins.partials.footer')
        </section>

    </div>

    {{-- Các đoạn script dùng chung --}}
    <script src="{{ asset('admins/js/jquery1-3.4.1.min.js') }}"></script>
    <script src="{{ asset('admins/js/bootstrap1.min.js') }}"></script>
    <script src="{{ asset('admins/js/custom.js') }}"></script>
    <script src="{{ asset('admins/js/metisMenu.js') }}"></script>
    <script src="{{ asset('admins/js/dashboard_init.js') }}"></script>
    <!-- Nếu có thêm js custom thì thêm ở đây -->
    @yield('JS')
</body>

</html>
