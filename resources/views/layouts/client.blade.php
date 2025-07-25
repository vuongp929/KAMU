<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Thế giới thú bông!">
    <meta name="author" content="ChillFriend">

    <title>@yield('title', 'ChillFriend')</title>

    <!-- GOOGLE FONTS -->
    <link href="http://fonts.googleapis.com/css?family=Roboto:400,500,700,300,100" rel="stylesheet" type="text/css">

    <!-- PLUGINS CSS -->
    <link href="{{ asset('assets/clients/plugins/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/clients/plugins/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/clients/plugins/fancybox/source/jquery.fancybox.css') }}" rel="stylesheet" type="text/css" />

    {{-- <script src="{{ asset('assets/clients/bootstrap/bootstrap-touchspin/bootstrap.touchspin.css') }}"></script>
    <script src="{{ asset('assets/clients/bootstrap/bootstrap-touchspin/bootstrap.touchspin.css') }}"></script> --}}
    {{-- <link href="{{ asset('assets/clients/bootstrap/bootstrap-touchspin/bootstrap.touchspin.css') }}">
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
    <link href="{{ asset('assets/clients/pages/css/style-header.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/clients/pages/css/animate.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/clients/pages/css/components.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/clients/pages/css/gallery.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/clients/pages/css/portfolio.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/clients/pages/css/slider.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/clients/pages/css/style-shop.css') }}" rel="stylesheet" type="text/css"> --}}
    
    {{-- CSS riêng cho từng trang --}}
    @yield('CSS')

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@400;600;700&family=Pacifico&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.js" />

    <!-- THEME STYLES -->
    <link href="{{ asset('assets/clients/pages/css/components.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/clients/corporate/css/style.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/clients/corporate/css/style-responsive.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/clients/corporate/css/themes/red.css') }}" rel="stylesheet" type="text/css" id="style-color" />
    <link href="{{ asset('assets/clients/corporate/css/custom.css') }}" rel="stylesheet" type="text/css" />

    {{-- CSS TÙY CHỈNH CHO TỪNG TRANG --}}
    @stack('styles')
</head>
<body class="ecommerce">

    {{-- Header --}}
    @include('clients.layouts.header')

    {{-- Nội dung chính của trang sẽ được chèn vào đây --}}
    @yield('content')

    {{-- Pre-footer (nếu có) --}}
    @include('clients.layouts.pre-footer')

    {{-- Footer --}}
    @include('clients.layouts.footer')

    {{-- Div ẩn cho pop-up xem nhanh --}}
    <div id="product-pop-up" style="display: none; width: 700px;"></div>


    <!-- ================================================== -->
    <!--  BẮT ĐẦU KHỐI SCRIPT (QUAN TRỌNG) -->
    <!-- ================================================== -->

    {{-- 1. Tải jQuery LÊN ĐẦU TIÊN --}}
    <script src="{{ asset('assets/clients/plugins/jquery.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/clients/plugins/jquery-migrate.min.js') }}" type="text/javascript"></script>

    {{-- 2. Tải Bootstrap JS --}}
    <script src="{{ asset('assets/clients/plugins/bootstrap/js/bootstrap.min.js') }}" type="text/javascript"></script>

    {{-- 3. Tải các plugin khác --}}
    <script src="{{ asset('assets/clients/plugins/fancybox/source/jquery.fancybox.pack.js') }}" type="text/javascript"></script>
    {{-- Nếu bạn dùng Owl Carousel, hãy thêm JS của nó vào đây --}}
    {{-- <script src="{{ asset('assets/clients/plugins/owl.carousel/owl.carousel.min.js') }}" type="text/javascript"></script> --}}

    {{-- 4. Tải các script chính của template --}}
    <script src="{{ asset('assets/clients/corporate/scripts/layout.js') }}" type="text/javascript"></script>
    <script type="text/javascript">
        jQuery(document).ready(function() {
            Layout.init(); // Khởi tạo các chức năng cơ bản của layout
            // Layout.initOWL(); // Nếu bạn dùng Owl Carousel
            Layout.initFancybox(); // Kích hoạt chức năng phóng to ảnh mặc định của Fancybox
        });
    </script>

    {{-- 5. Vị trí để nhúng các script của từng trang riêng biệt --}}
    @stack('scripts')

</body>
</html>