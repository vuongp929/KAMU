@push('styles')
<link rel="stylesheet" href="{{ asset('css/custom-dropdown.css') }}">
<style>
        /* CSS phong cách "Ôm Là Yêu" */
        header { font-family: 'Baloo 2', cursive; color: #5d3b80; }
        .header-top { background-color: #fff; padding: 15px 0; border-bottom: 2px solid #fde2f3; }
        .logo-link { font-family: 'Pacifico', cursive; font-size: 2.5rem; color: #ea73ac; text-decoration: none; line-height: 1; }
        .search-form { border: 2px solid #fde2f3; border-radius: 50px; overflow: hidden; display: flex; }
        .search-input { border: none; flex-grow: 1; padding: 10px 20px; outline: none; font-size: 14px; background-color: #fffafc; }
        .search-btn { background-color: #ea73ac; color: white; border: none; padding: 10px 20px; cursor: pointer; }
        .header-actions .btn-link { color: #5d3b80; text-decoration: none; font-weight: 600; font-size: 15px; }
        .header-actions .btn-link:hover, .header-actions .dropdown-toggle:hover { color: #ea73ac; }
        .cart-icon-wrapper { position: relative; margin-left: 15px; }
        .cart-link { color: #5d3b80; font-size: 26px; }
        .cart-count { position: absolute; top: -8px; right: -12px; background-color: #ff5c5c; color: white; font-size: 12px; font-weight: bold; line-height: 1; padding: 4px 7px; border-radius: 50%; border: 2px solid white; }
        .header-nav { background-color: #fffafc; box-shadow: 0 4px 10px rgba(0,0,0,0.05); padding: 8px 0; }
        .navbar-nav .nav-link { 
            color: #5d3b80; 
            font-weight: 700; 
            text-transform: uppercase; 
            padding: 18px 25px; 
            border-radius: 8px; 
            font-size: 16px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        .navbar-nav .nav-link::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(234, 115, 172, 0.1), transparent);
            transition: left 0.5s ease;
        }
        .navbar-nav .nav-link:hover::before {
            left: 100%;
        }
        .navbar-nav .nav-link:hover, .navbar-nav .nav-item.dropdown:hover .nav-link { 
            color: #ea73ac; 
            background-color: #fff0f5; 
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(234, 115, 172, 0.2);
        }
        .dropdown-menu { 
            border: none; 
            border-radius: 12px; 
            box-shadow: 0 8px 25px rgba(0,0,0,0.15); 
            margin-top: 8px;
            padding: 10px 0;
            min-width: 200px;
            animation: fadeInDown 0.3s ease;
            background-color: #ffffff;
            z-index: 1000;
            display: none;
        }
        .nav-item.dropdown:hover .dropdown-menu {
            display: block;
        }
        .dropdown:hover .dropdown-menu {
            display: block;
        }
        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .dropdown-item { 
            color: #5d3b80; 
            font-weight: 600; 
            padding: 12px 25px;
            font-size: 15px;
            transition: all 0.2s ease;
            position: relative;
        }
        .dropdown-item::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 3px;
            background-color: #ea73ac;
            transform: scaleY(0);
            transition: transform 0.2s ease;
        }
        .dropdown-item:hover { 
            background-color: #fff0f5; 
            color: #ea73ac;
            padding-left: 30px;
        }
        .dropdown-item:hover::before {
            transform: scaleY(1);
        }
        .navbar-toggler {
            border: 2px solid #ea73ac;
            padding: 8px 12px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        .navbar-toggler:hover {
            background-color: #ea73ac;
            transform: scale(1.05);
        }
        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='%23ea73ac' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
            width: 24px;
            height: 24px;
        }
        .navbar-toggler:hover .navbar-toggler-icon {
             background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='%23ffffff' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
         }
         
         /* Dropdown functionality */
         .dropdown-toggle::after {
             border-top: 0.3em solid;
             border-right: 0.3em solid transparent;
             border-bottom: 0;
             border-left: 0.3em solid transparent;
             margin-left: 0.5em;
             vertical-align: 0.1em;
         }
         
         .nav-item.dropdown .dropdown-menu {
             position: absolute;
             top: 100%;
             left: 0;
         }
         
         .dropdown-menu.show {
             display: block;
         }
         
         /* Responsive Design */
         @media (max-width: 991.98px) {
             .navbar-nav .nav-link {
                 padding: 15px 20px;
                 font-size: 15px;
                 border-radius: 6px;
                 margin: 2px 0;
             }
             .dropdown-menu {
                 position: static !important;
                 transform: none !important;
                 box-shadow: inset 0 2px 8px rgba(234, 115, 172, 0.1);
                 border-radius: 8px;
                 margin: 5px 15px;
                 background-color: #fef7fb;
                 display: block !important;
             }
             .nav-item.dropdown:hover .dropdown-menu {
                 display: block !important;
             }
             .dropdown-item {
                 padding: 10px 20px;
                 font-size: 14px;
             }
             .navbar-collapse {
                 background-color: #fffafc;
                 border-radius: 12px;
                 margin-top: 10px;
                 padding: 15px;
                 box-shadow: 0 4px 15px rgba(0,0,0,0.1);
             }
         }
         
         @media (max-width: 767.98px) {
             .header-top {
                 padding: 10px 0;
             }
             .logo-link {
                 font-size: 2rem;
             }
             .navbar-nav .nav-link {
                 padding: 12px 15px;
                 font-size: 14px;
                 text-align: center;
             }
             .dropdown-item {
                 padding: 8px 15px;
                 font-size: 13px;
             }
             .header-actions {
                 justify-content: center !important;
                 margin-top: 10px;
             }
             .cart-icon-wrapper {
                 margin-left: 10px;
             }
         }
    </style>
{{-- Bắt đầu phần HTML --}}
<header>
    <!-- Phần trên cùng -->
    <div class="header-top">
        <div class="container">
            <div class="row align-items-center">
                <!-- Logo -->
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <a href="{{ route('home') }}" class="logo-link d-flex align-items-center">
                        <img src="{{ asset('images/logo.png') }}" alt="KUMA House Logo" style="height: 40px; margin-right: 10px;">
                        KUMA House
                    </a>
                </div>

                <!-- Thanh tìm kiếm -->
                <div class="col-lg-5 d-none d-lg-block">
                    <form action="{{ route('client.products.index') }}" method="GET" class="search-form">
                        <input type="text" name="search" placeholder="Bạn đang tìm bé gấu nào?..." class="search-input" value="{{ request('search') }}">
                        <button type="submit" class="search-btn"><i class="fas fa-search"></i></button>
                    </form>

                </div>

                <!-- Nút chức năng -->
                <div class="col-lg-4 col-md-8 col-sm-6 d-flex justify-content-end align-items-center header-actions">
                    @guest
                        {{-- Khi chưa đăng nhập --}}
                        <a href="{{ route('login') }}" class="btn btn-link"><i class="fas fa-user-circle me-1"></i> Đăng nhập</a>
                        <span class="mx-2">/</span>
                        <a href="{{ route('register') }}" class="btn btn-link">Đăng ký</a>
                    @else
                        {{-- Khi đã đăng nhập --}}
                        <div class="dropdown">
                            <a href="#" class="btn btn-link dropdown-toggle" id="userMenuDropdown" role="button" data-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user me-1"></i> Chào, {{ Auth::user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="userMenuDropdown">
                                <li><a class="dropdown-item" href="{{ route('profile.edit') }}">Thông tin cá nhân</a></li>
                                <li><a class="dropdown-item" href="{{ route('client.orders.index') }}">Đơn hàng của tôi</a></li>
                                <li><a class="dropdown-item" href="{{ route('wishlist.index') }}">Sản phẩm yêu thích</a></li>
                                @if(Auth::user()->isAdmin()) {{-- Giả sử có hàm isAdmin() trong model User --}}
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}">Trang quản trị</a></li>
                                @endif
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('logout') }}" 
                                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <i class="fas fa-sign-out-alt me-2"></i> Đăng xuất
                                    </a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
                                </li>
                            </ul>
                        </div>
                    @endguest
                
                    <div class="cart-icon-wrapper">
                        {{-- Sửa lại route giỏ hàng cho đúng --}}
                        <a href="{{ route('client.cart.index') }}" class="cart-link" title="Giỏ hàng">
                            <i class="fas fa-shopping-bag"></i>
                            {{-- TODO: Logic đếm số lượng trong giỏ hàng cần được truyền từ controller hoặc View Composer --}}
                            @php $cartCount = session('cart') ? count(session('cart')) : 0; @endphp
                            @if($cartCount > 0)
                                <span class="cart-count">{{ $cartCount }}</span>
                            @endif
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Menu danh mục -->
    <nav class="navbar navbar-expand-lg header-nav">
        <div class="container">
            <button class="navbar-toggler d-lg-none ms-auto" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="mainNavbar">
                <ul class="navbar-nav mx-auto">
                    <!-- Trang chủ -->
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('home') }}">Trang chủ</a>
                    </li>

                    <!-- Danh mục động từ database -->
                    @if(isset($categoriesForMenu))
                        @foreach($categoriesForMenu as $category)
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="{{ route('client.products.index', ['category' => $category->slug]) }}" data-bs-toggle="dropdown">{{ $category->name }}</a>
                                @if($category->activeChildren->isNotEmpty())
                                    <ul class="dropdown-menu">
                                        @foreach($category->activeChildren as $child)
                                            <li><a class="dropdown-item" href="{{ route('client.products.index', ['category' => $child->slug]) }}">{{ $child->name }}</a></li>
                                        @endforeach
                                    </ul>
                                @endif
                            </li>
                        @endforeach
                    @endif

                    <!-- Tất cả sản phẩm -->
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('client.products.index') }}">Tất cả sản phẩm</a>
                    </li>

                    <!-- Dịch vụ -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Dịch vụ</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('client.services.guide') }}">Hướng dẫn mua hàng</a></li>
                            <li><a class="dropdown-item" href="{{ route('client.services.washing') }}">Dịch vụ giặt gấu</a></li>
                            <li><a class="dropdown-item" href="{{ route('client.services.gift-wrap') }}">Gói quà siêu đẹp</a></li>
                            <li><a class="dropdown-item" href="{{ route('client.services.free-card') }}">Tặng thiệp miễn phí</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

</header>

{{-- Script cần thiết cho dropdown của Bootstrap --}}
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Enhanced dropdown handling for desktop
            if (window.innerWidth > 991) {
                var dropdowns = document.querySelectorAll('.nav-item.dropdown');
                
                dropdowns.forEach(function(dropdown) {
                    var dropdownMenu = dropdown.querySelector('.dropdown-menu');
                    var hoverTimeout;
                    
                    dropdown.addEventListener('mouseenter', function() {
                        clearTimeout(hoverTimeout);
                        if (dropdownMenu) {
                            dropdownMenu.style.display = 'block';
                            setTimeout(() => {
                                dropdownMenu.classList.add('show');
                            }, 10);
                        }
                    });
                    
                    dropdown.addEventListener('mouseleave', function() {
                        if (dropdownMenu) {
                            dropdownMenu.classList.remove('show');
                            hoverTimeout = setTimeout(() => {
                                dropdownMenu.style.display = 'none';
                            }, 300);
                        }
                    });
                });
            }
            
            // Add smooth scroll effect for dropdown items
            var dropdownItems = document.querySelectorAll('.dropdown-item');
            dropdownItems.forEach(function(item) {
                item.addEventListener('click', function(e) {
                    // Add ripple effect
                    var ripple = document.createElement('span');
                    ripple.classList.add('ripple');
                    this.appendChild(ripple);
                    
                    setTimeout(() => {
                        ripple.remove();
                    }, 600);
                });
            });
        });
    </script>
@endpush