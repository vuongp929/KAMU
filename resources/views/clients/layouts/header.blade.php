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
        .header-nav { background-color: #fffafc; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
        .navbar-nav .nav-link { color: #5d3b80; font-weight: 700; text-transform: uppercase; padding: 15px 20px; border-radius: 8px; }
        .navbar-nav .nav-link:hover, .navbar-nav .nav-item.dropdown:hover .nav-link { color: #ea73ac; background-color: #fff0f5; }
        .dropdown-menu { border: none; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); margin-top: 5px; }
        .dropdown-item { color: #5d3b80; font-weight: 600; padding: 10px 20px; }
        .dropdown-item:hover { background-color: #fff0f5; color: #ea73ac; }
    </style>
{{-- Bắt đầu phần HTML --}}
<header>
    <!-- Phần trên cùng -->
    <div class="header-top">
        <div class="container">
            <div class="row align-items-center">
                <!-- Logo -->
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <a href="{{ route('home') }}" class="logo-link">KUMA House</a>
                </div>

                <!-- Thanh tìm kiếm -->
                <div class="col-lg-5 d-none d-lg-block">
                    <form action="{{ route('clients.search') }}" method="GET" class="search-form">
                        <input type="text" name="query" placeholder="Bạn đang tìm bé gấu nào?..." class="search-input">
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
                            <a href="#" class="btn btn-link dropdown-toggle" id="userMenuDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user me-1"></i> Chào, {{ Auth::user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userMenuDropdown">
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
    {{-- <nav class="navbar navbar-expand-lg header-nav">
        <div class="container">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="mainNavbar">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item"><a class="nav-link" href="{{ route('home') }}">Trang chủ</a></li>
                   
                    @if(isset($categoriesForMenu))
                        @foreach($categoriesForMenu as $category)
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">{{ $category->name }}</a>
                                @if($category->children->isNotEmpty())
                                    <ul class="dropdown-menu">
                                        @foreach($category->children as $child)
                                            <li><a class="dropdown-item" href="#">{{ $child->name }}</a></li>
                                        @endforeach
                                    </ul>
                                @endif
                            </li>
                        @endforeach
                    @endif
                    <li class="nav-item"><a class="nav-link" href="#">Tất cả sản phẩm</a></li>
                </ul>
            </div>
        </div>
    </nav> --}}
        <nav class="navbar navbar-expand-lg header-nav">
        <div class="container">
            <button class="navbar-toggler d-lg-none ms-auto" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="mainNavbar">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('home') }}">Trang chủ</a>
                    </li>

                    @if(isset($categoriesForMenu))
                        {{-- @foreach($categoriesForMenu as $category)
                            @if($category->activeChildren->isNotEmpty())
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">{{ $category->name }}</a>
                                    <ul class="dropdown-menu">
                                        @foreach($category->activeChildren as $child)
                                            <li>
                                                <a class="dropdown-item" href="#">{{ $child->name }}</a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </li>
                            @else
                                <li class="nav-item">
                                    <a class="nav-link" href="#">{{ $category->name }}</a>
                                </li>
                            @endif
                        @endforeach --}}
                        @foreach($categoriesForMenu as $category)
                            @if($category->statu == 1)
                                {{-- Render menu --}}
                                @if($category->activeChildren->isNotEmpty())
                                    <li class="nav-item dropdown">
                                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">{{ $category->name }}</a>
                                        <ul class="dropdown-menu">
                                            @foreach($category->activeChildren as $child)
                                                <li><a class="dropdown-item" href="#">{{ $child->name }}</a></li>
                                            @endforeach
                                        </ul>
                                    </li>
                                @else
                                    <li class="nav-item"><a class="nav-link" href="#">{{ $category->name }}</a></li>
                                @endif
                            @endif
                        @endforeach

                    @endif

                    <li class="nav-item">
                        <a class="nav-link" href="#">Tất cả sản phẩm</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

</header>

{{-- Script cần thiết cho dropdown của Bootstrap --}}
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@endpush