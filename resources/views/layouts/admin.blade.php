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
    <link rel="shortcut icon" href="{{ asset('admins/img/mini_logo.png') }}">
    <link rel="shortcut icon" href="{{ asset('admins/img/logo.png') }}">

    <!-- Bootstrap Css -->
    <link rel="stylesheet" href="{{ asset('admins/css/bootstrap1.min.css') }}" />
    <!-- Icons Css -->
    <link href="{{ asset('assets/admins/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- App Css-->
    <link href="{{ asset('assets/admins/css/app.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- custom Css-->
    <link href="{{ asset('assets/admins/css/custom.min.css') }}" rel="stylesheet" type="text/css" />

    <!-- Custom fonts -->
    <link href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Custom styles -->
    <link href="{{ asset('css/sb-admin-2.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('admins/vendors/themefy_icon/themify-icons.css') }}" />
    <link rel="stylesheet" href="{{ asset('admins/vendors/niceselect/css/nice-select.css') }}" />
    <link rel="stylesheet" href="{{ asset('admins/vendors/owl_carousel/css/owl.carousel.css') }}" />
    <link rel="stylesheet" href="{{ asset('admins/vendors/gijgo/gijgo.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('admins/vendors/font_awesome/css/all.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('admins/vendors/tagsinput/tagsinput.css') }}" />
    <link rel="stylesheet" href="{{ asset('admins/vendors/datepicker/date-picker.css') }}" />
    <link rel="stylesheet" href="{{ asset('admins/vendors/vectormap-home/vectormap-2.0.2.css') }}" />
    <link rel="stylesheet" href="{{ asset('admins/vendors/scroll/scrollable.css') }}" />
    <link rel="stylesheet" href="{{ asset('admins/vendors/datatable/css/jquery.dataTables.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('admins/vendors/datatable/css/responsive.dataTables.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('admins/vendors/datatable/css/buttons.dataTables.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('admins/vendors/text_editor/summernote-bs4.css') }}" />
    <link rel="stylesheet" href="{{ asset('admins/vendors/morris/morris.css') }}">
    <link rel="stylesheet" href="{{ asset('admins/vendors/material_icon/material-icons.css') }}" />
    <link rel="stylesheet" href="{{ asset('admins/css/metisMenu.css') }}">
    <link rel="stylesheet" href="{{ asset('admins/css/style1.css') }}" />
    <link rel="stylesheet" href="{{ asset('admins/css/colors/default.css') }}" id="colorSkinCSS">
    @stack('styles')
</head>

<body class="crm_body_bg" style="padding-top: 80px;">
    @include('admins.partials.siderbar')
    <section class="main_content dashboard_part large_header_bg">
        @include('admins.partials.header')
        <div class="main_content_iner overly_inner ">
            @yield('content')
        </div>
        @include('admins.partials.footer')
    </section>
    <div id="back-top" style="display: none;">
        <a title="Go to Top" href="#">
            <i class="ti-angle-up"></i>
        </a>
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

        @include('admins.partials.header')
        @include('admins.partials.siderbar')

        <div class="vertical-overlay"></div>

        <section class="main_content">
            @include('admins.partials.header')
            <div class="main_content_iner">
                @yield('content')
            </div>

            @include('admins.partials.footer')

        </div>
            @include('admins.partials.footer')
        </section>
    </div>

    <script src="{{ asset('admins/js/jquery1-3.4.1.min.js') }}"></script>
    <script src="{{ asset('admins/js/popper1.min.js') }}"></script>
    <script src="{{ asset('admins/js/bootstrap1.min.js') }}"></script>
    <script src="{{ asset('admins/js/metisMenu.js') }}"></script>
    <script src="{{ asset('admins/vendors/count_up/jquery.waypoints.min.js') }}"></script>
    <script src="{{ asset('admins/vendors/chartlist/Chart.min.js') }}"></script>
    <script src="{{ asset('admins/vendors/count_up/jquery.counterup.min.js') }}"></script>
    <script src="{{ asset('admins/vendors/niceselect/js/jquery.nice-select.min.js') }}"></script>
    <script src="{{ asset('admins/vendors/owl_carousel/js/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('admins/vendors/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('admins/vendors/datatable/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('admins/vendors/datatable/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('admins/vendors/datatable/js/buttons.flash.min.js') }}"></script>
    <script src="{{ asset('admins/vendors/datatable/js/jszip.min.js') }}"></script>
    <script src="{{ asset('admins/vendors/datatable/js/pdfmake.min.js') }}"></script>
    <script src="{{ asset('admins/vendors/datatable/js/vfs_fonts.js') }}"></script>
    <script src="{{ asset('admins/vendors/datatable/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('admins/vendors/datatable/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('admins/vendors/datepicker/datepicker.js') }}"></script>
    <script src="{{ asset('admins/vendors/datepicker/datepicker.en.js') }}"></script>
    <script src="{{ asset('admins/vendors/datepicker/datepicker.custom.js') }}"></script>
    <script src="{{ asset('admins/js/chart.min.js') }}"></script>
    <script src="{{ asset('admins/vendors/chartjs/roundedBar.min.js') }}"></script>
    <script src="{{ asset('admins/vendors/progressbar/jquery.barfiller.js') }}"></script>
    <script src="{{ asset('admins/vendors/tagsinput/tagsinput.js') }}"></script>
    <script src="{{ asset('admins/vendors/text_editor/summernote-bs4.js') }}"></script>
    <script src="{{ asset('admins/vendors/am_chart/amcharts.js') }}"></script>
    <script src="{{ asset('admins/vendors/scroll/perfect-scrollbar.min.js') }}"></script>
    <script src="{{ asset('admins/vendors/scroll/scrollable-custom.js') }}"></script>
    <script src="{{ asset('admins/vendors/vectormap-home/vectormap-2.0.2.min.js') }}"></script>
    <script src="{{ asset('admins/vendors/vectormap-home/vectormap-world-mill-en.js') }}"></script>
    <script src="{{ asset('admins/vendors/apex_chart/apex-chart2.js') }}"></script>
    <script src="{{ asset('admins/vendors/apex_chart/apex_dashboard.js') }}"></script>
    <script src="{{ asset('admins/vendors/echart/echarts.min.js') }}"></script>
    <script src="{{ asset('admins/vendors/chart_am/core.js') }}"></script>
    <script src="{{ asset('admins/vendors/chart_am/charts.js') }}"></script>
    <script src="{{ asset('admins/vendors/chart_am/animated.js') }}"></script>
    <script src="{{ asset('admins/vendors/chart_am/kelly.js') }}"></script>
    <script src="{{ asset('admins/vendors/chart_am/chart-custom.js') }}"></script>
    <script src="{{ asset('admins/js/dashboard_init.js') }}"></script>
    <script src="{{ asset('admins/js/custom.js') }}"></script>
    <script src="{{ asset('admins/js/bootstrap1.min.js') }}"></script>
    <script src="{{ asset('admins/js/custom.js') }}"></script>
    <script src="{{ asset('admins/js/metisMenu.js') }}"></script>
    <script src="{{ asset('admins/js/dashboard_init.js') }}"></script>
    @yield('JS')
</body>

</html>
