@extends('layouts.client')

@section('title', 'Trang chủ - Ôm Là Yêu')

@section('content')
<div class="main home-page">
    <div class="container-fluid px-0">
        {{-- SECTION 1: BANNER CHÍNH --}}
        <div class="row banner-section no-gutters">
            <div class="col-md-8 px-0">
                {{-- Banner chính ở giữa --}}
                <div class="main-banner">
                    <img src="https://teddy.vn/wp-content/uploads/2024/01/banner-thuong_DC.jpg" alt="Gấu Bông An Toàn Cao Cấp Hot Trend" class="img-responsive">
                </div>
            </div>
            <div class="col-md-4 px-0">
                {{-- Banner phụ bên phải --}}
                <div class="side-banners">
                    <div class="side-banner-item">
                        <img src="https://teddy.vn/wp-content/uploads/2024/01/banner-thuong_Dien-gau.jpg" alt="Điểm Gấu Tại Nhà - Tặng Vàng Thưởng Vui Tủy" class="img-responsive">
                    </div>
                    <div class="side-banner-item">
                        <img src="https://teddy.vn/wp-content/uploads/2024/01/banner-thuong_Dich-vu.jpg" alt="Teddy With Love" class="img-responsive">
                    </div>
                </div>
            </div>
        </div>

        {{-- SECTION 2: ICON DỊCH VỤ --}}
        <div class="row services-section text-center">
            <div class="col-md-3 col-sm-6">
                <a href="{{ route('giao-hang') }}" class="text-decoration-none text-dark">
                    <div class="service-item">
                        <img src="https://teddy.vn/wp-content/uploads/2017/07/Artboard-16-1-e1661254198715.png" alt="Giao Hàng Tận Nhà">
                        <h4>GIAO HÀNG TẬN NHÀ</h4>
                    </div>
                </a>
            </div>
            <div class="col-md-3 col-sm-6">
                <a href="{{ route('dich-vu-goi-qua') }}" class="text-decoration-none text-dark">
                    <div class="service-item">
                        <img src="https://teddy.vn/wp-content/uploads/2017/07/Artboard-16-copy-1.png" alt="Gói Quà Siêu Đẹp">
                        <h4>GÓI QUÀ SIÊU ĐẸP</h4>
                    </div>
                </a>
            </div>
            <div class="col-md-3 col-sm-6">
                <a href="{{ url('cach-giat-gau-bong') }}" class="text-decoration-none text-dark">
                    <div class="service-item">
                        <img src="https://teddy.vn/wp-content/uploads/2017/07/Artboard-16-copy-2-1.png" alt="Cách Giặt Gấu Bông">
                        <h4>CÁCH GIẶT GẤU BÔNG</h4>
                    </div>
                </a>
            </div>
            <div class="col-md-3 col-sm-6">
                <a href="{{ url('chinh-sach-doi-tra') }}" class="text-decoration-none text-dark">
                    <div class="service-item">
                        <img src="https://teddy.vn/wp-content/uploads/2018/04/Artboard-16-copy-3-1.png" alt="Bảo Hành Gấu Bông">
                        <h4>BẢO HÀNH GẤU BÔNG</h4>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <div class="container">
        {{-- SECTION 3: SẢN PHẨM MỚI (GẤU BÔNG HOT TREND) --}}
        <div class="row product-section">
            <div class="col-md-12 text-center">
                <div class="section-title">
                    <span>GẤU BÔNG HOT TREND</span>
                </div>
            </div>
            
            @forelse($newProducts as $product)
                <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
                    {{-- Sửa lại product-card để có thêm size --}}
                    @include('clients.product-card', ['product' => $product])
                </div>
            @empty
                <p class="text-center col-xs-12">Chưa có sản phẩm mới nào.</p>
            @endforelse

            <div class="col-md-12 text-center">
                <a href="#" class="btn btn-view-more">XEM THÊM GẤU BÔNG HOT TREND <i class="fa fa-angle-double-right"></i></a>
            </div>
        </div>

        {{-- SECTION 4: SẢN PHẨM NỔI BẬT (BST HOA GẤU BÔNG) --}}
        <div class="row product-section">
            <div class="col-md-12 text-center">
                <div class="section-title">
                    <span>BST HOA GẤU BÔNG</span>
                </div>
            </div>
            
            @forelse($featuredProducts as $product)
                 <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
                    @include('clients.product-card', ['product' => $product])
                </div>
            @empty
                <p class="text-center col-xs-12">Chưa có sản phẩm nổi bật nào.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection