@extends('layouts.client')

@section('title', 'Trang chủ')

@section('content')
{{-- Phần slider của bạn giữ nguyên --}}
<div class="page-slider margin-bottom-35">
    {{-- Nội dung slider của bạn --}}
</div>

<div class="main">
    <div class="container">
        <!-- SẢN PHẨM MỚI NHẤT -->
        <div class="row margin-bottom-40">
            <div class="col-md-12">
                <h2>Sản phẩm mới nhất</h2>
                <hr>
            </div>
            {{-- Dùng cấu trúc lưới của Bootstrap --}}
            <div class="row product-list">
                @forelse($newProducts->take(4) as $product) {{-- Chỉ lấy 4 sản phẩm cho hàng đầu tiên --}}
                    <div class="col-md-3 col-sm-6 col-xs-12">
                        @include('clients.product-card', ['product' => $product])
                    </div>
                @empty
                    <p class="text-center col-xs-12">Chưa có sản phẩm mới nào.</p>
                @endforelse
            </div>
            {{-- Nếu bạn muốn có 2 hàng sản phẩm mới --}}
            <div class="row product-list">
                 @forelse($newProducts->skip(4)->take(4) as $product) {{-- Lấy 4 sản phẩm tiếp theo --}}
                    <div class="col-md-3 col-sm-6 col-xs-12">
                        @include('clients.product-card', ['product' => $product])
                    </div>
                @empty
                    {{-- Không cần hiển thị gì ở đây --}}
                @endforelse
            </div>
        </div>

        <!-- KHÁM PHÁ DANH MỤC -->
        <div class="row margin-bottom-35">
             <div class="col-md-12">
                <h2 class="text-center">Khám phá Danh mục</h2>
                <hr>
            </div>
            @foreach($categories as $category)
                <div class="col-md-4 col-sm-6 margin-bottom-20">
                    <a href="#">
                        {{-- Giữ nguyên cấu trúc này vì nó có vẻ phù hợp với template --}}
                        <div class="img-hover-effect">
                            <img src="{{ $category->image ? Storage::url($category->image) : 'https://via.placeholder.com/360x200' }}" class="img-responsive" alt="{{ $category->name }}">
                            <div class="overlay">
                                <h3>{{ $category->name }}</h3>
                                <p>{{ $category->products_count }} sản phẩm</p>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>

        <!-- SẢN PHẨM NỔI BẬT -->
        <div class="row margin-bottom-40">
             <div class="col-md-12">
                <h2>Sản phẩm nổi bật</h2>
                <hr>
            </div>
            <div class="row product-list">
                 @forelse($featuredProducts as $product)
                    <div class="col-md-3 col-sm-6 col-xs-12">
                        @include('clients.product-card', ['product' => $product])
                    </div>
                @empty
                    <p class="text-center col-xs-12">Chưa có sản phẩm nổi bật nào.</p>
                @endforelse
            </div>
        </div>
        
    </div>
</div>
@endsection

@section('page_scripts')
{{-- Chúng ta không cần script cho Owl Carousel nữa --}}
@endsection