<!DOCTYPE html>
<html lang="vi">

<head>

    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>@yield('title')</title>
    <link rel="icon" href="img/mini_logo.png" type="image/png">

    <link rel="stylesheet" href="{{ asset('admins/css/bootstrap1.min.css') }}" />

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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

</head>

<body class="crm_body_bg">
    {{-- Navbar --}}
    @include('admins.partials.siderbar')
    {{-- Sidebar --}}

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
    <script src="{{ asset('admins/vendors/datatable/js/pdfmake.min.j') }}s"></script>
    <script src="{{ asset('admins/vendors/datatable/js/vfs_fonts.js') }}"></script>
    <script src="{{ asset('admins/vendors/datatable/js/buttons.html5.min.j') }}s"></script>
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

    <script src="{{ asset('admins/vendors/apex_chart/apex-chart2.j') }}s"></script>
    <script src="{{ asset('admins/vendors/apex_chart/apex_dashboard.js') }}"></script>
    <script src="{{ asset('admins/vendors/echart/echarts.min.js') }}"></script>
    <script src="{{ asset('admins/vendors/chart_am/core.js') }}"></script>
    <script src="{{ asset('admins/vendors/chart_am/charts.js') }}"></script>
    <script src="{{ asset('admins/vendors/chart_am/animated.js') }}"></script>
    <script src="{{ asset('admins/vendors/chart_am/kelly.js') }}"></script>
    <script src="{{ asset('admins/vendors/chart_am/chart-custom.js') }}"></script>

    <script src="{{ asset('admins/js/dashboard_init.js') }}"></script>
    <script src="{{ asset('admins/js/custom.js') }}"></script>
    {{-- <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script> --}}

</body>

</html>
