<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" 
      data-layout="vertical" 
      data-topbar="light" 
      data-sidebar="dark" 
      data-sidebar-size="lg" 
      data-sidebar-image="none" 
      data-preloader="disable">

<head>
    <meta charset="utf-8" />
    <title>@yield('title') | Admin Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('assets_velzon/images/favicon.ico') }}">

    {{-- 
        Vite sẽ tự động nạp TẤT CẢ các file CSS và JS
        mà chúng ta đã định nghĩa trong vite.config.js
    --}}
    @vite([
        // CSS
        'resources/assets_velzon/css/bootstrap.min.css',
        'resources/assets_velzon/css/icons.min.css',
        'resources/assets_velzon/css/app.min.css',
        'resources/assets_velzon/css/custom.min.css',
        'resources/js/app.js', // File JS chung của bạn, nạp ở đây để đảm bảo DOM sẵn sàng

        // JS
        'resources/assets_velzon/libs/bootstrap/js/bootstrap.bundle.min.js',
        'resources/assets_velzon/libs/simplebar/simplebar.min.js',
        'resources/assets_velzon/libs/node-waves/waves.min.js',
        'resources/assets_velzon/libs/feather-icons/feather.min.js',
        'resources/assets_velzon/js/pages/plugins/lord-icon-2.1.0.js',
        'resources/assets_velzon/js/plugins.js',
        'resources/assets_velzon/js/app.js', // File app.js chính của Velzon
    ])

    {{-- Điểm neo cho các trang con có thể thêm CSS riêng --}}
    @stack('styles')
</head>

<body>
    <!-- Bắt đầu trang -->
    <div id="layout-wrapper">

        @include('admins.blocks.header')

        @include('admins.blocks.siderbar')

        <div class="main-content">
            <div class="page-content">
                <div class="container-fluid">
                    @yield('content')
                </div>
            </div>
            @include('admins.blocks.footer')
        </div>

    </div>
    <!-- KẾT THÚC layout-wrapper -->

    {{-- Điểm neo cho các trang con có thể thêm JS riêng (ví dụ: trang chat) --}}
    @stack('scripts')
</body>
</html>