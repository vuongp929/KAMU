<style>
    body {
    font-family: 'VL BoosterNextFYBlack', sans-serif;
    color: #8357ae;
    
}
h1, h2, h3, h4, h5, h6 {
    font-weight: bold;
    color: #8357ae;
}
.header-top {
    background-color: #f8f9fa;
    padding: 10px 0;
}

.logo {
    font-size: 1.8rem;
    font-weight: bold;
    color: #8357ae;
    text-decoration: none;
    font-family: 'Pacifico', cursive;
}

.search-form {
    display: flex;
    width: 100%;
}

.search-input {
    flex-grow: 1;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 5px !important;
    outline: none;
}

.search-btn {
    padding: 10px 20px;
    background-color: #8357ae;
    color: white;
    border: none;
    cursor: pointer;
}

.navbar {
    font-size: 20px;
}

/* Dropdown danh mục con */
.dropdown-menu {
    position: absolute;
    top: 100%;
    left: 0;
    z-index: 1000;
    font-size: 15px;
}

.dropdown:hover > .dropdown-menu {
    display: block;
}

.btn-link {
    color: #8357ae;
    font-size: 14px;
}
.nav-link{
    color: #8357ae;
    text-decoration: none;
    transition: color 0.3s ease;
    font-size: 14px;
    font-weight: 800;
    text-transform: uppercase;
    display: block;
    padding: 1rem .875rem .5rem;
    color: #8357ae;
    position: relative;
    font-family: 'Baloo 2', cursive;
}
.cart-icon-wrapper {
    position: relative;
    display: inline-block;
    margin-left: 15px;
}

.cart-link {
    text-decoration: none;
    color: #8357ae;
    font-size: 22px;
    position: relative;
}

.cart-count {
    background-color: #ff6b6b;
    color: white;
    font-size: 12px;
    font-weight: bold;
    padding: 2px 6px;
    border-radius: 50%;
    position: absolute;
    top: -5px;
    right: -10px;
}
</style>
<header>
    <!-- Phần trên cùng -->
     <div class="header-top py-2">
        <div class="container">
            <div class="row align-items-center">
                <!-- Logo -->
                <div class="col-md-3">
                    <a href="{{ route('home') }}" class="logo">ChillFriend</a>
                </div>

                <!-- Thanh tìm kiếm -->
                <div class="col-md-5">
                    <form action="#" method="GET" class="search-form">
                        <input type="text" name="query" placeholder="Tìm kiếm gấu bông..." class="search-input">
                        <button type="submit" class="search-btn"><i class="fas fa-search"></i></button>
                    </form>
                </div>

                <!-- Các nút chức năng -->
                <div class="col-md-4 d-flex justify-content-end align-items-center">
                    @guest
                        <a href="{{ route('login') }}" class="btn btn-link text-decoration-none">Đăng nhập</a>
                        <a href="{{ route('register') }}" class="btn btn-link text-decoration-none">Đăng ký</a>
                    @else
                        <div class="dropdown">
                            <a href="#" class="btn btn-link dropdown-toggle text-decoration-none" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Chào, {{ Auth::user()->name }}
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#">Tài khoản của tôi</a></li>
                                <li><a class="dropdown-item" href="#">Đơn hàng của tôi</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        Đăng xuất
                                    </a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </li>
                            </ul>
                        </div>
                    @endguest

                    <div class="cart-icon-wrapper">
                        <a href="#" class="cart-link">
                            <i class="fas fa-shopping-cart"></i>
                            {{-- Sử dụng biến $cartCount từ View Composer --}}
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
    <nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
        <div class="container">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto">
                    {{-- Dùng biến $categories từ View Composer --}}
                    @foreach($categories as $category)
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                {{ $category->name }}
                            </a>
                            {{-- Kiểm tra xem có danh mục con không --}}
                            @if($category->children->isNotEmpty())
                                <ul class="dropdown-menu">
                                    @foreach($category->children as $child)
                                        <li><a class="dropdown-item" href="#">{{ $child->name }}</a></li>
                                    @endforeach
                                </ul>
                            @endif
                        </li>
                    @endforeach
                    <li class="nav-item">
                        <a class="nav-link" href="#">Liên hệ</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</header>
