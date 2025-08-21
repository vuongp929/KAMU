@extends('layouts.client')

@section('title', 'Kết Quả Tìm Kiếm cho "' . request('query') . '"')

@section('content')
<div class="main">
    <div class="container py-5">
        
        {{-- TIÊU ĐỀ VÀ SỐ LƯỢNG KẾT QUẢ --}}
        <div class="row mb-4">
            <div class="col-12">
                <h2 class="search-title">Kết Quả Tìm Kiếm</h2>
                <p class="search-subtitle">
                    Tìm thấy <span class="fw-bold text-primary">{{ $products->total() }}</span> sản phẩm cho từ khóa 
                    <span class="fst-italic">"{{ request('query') }}"</span>
                </p>
                <hr>
            </div>
        </div>

        {{-- DANH SÁCH SẢN PHẨM --}}
        <div class="row product-list">
            @forelse($products as $product)
                <div class="col-lg-3 col-md-4 col-sm-6 col-12">
                    {{-- Tái sử dụng product-card để có giao diện đồng nhất --}}
                    @include('clients.product-card', ['product' => $product])
                </div>
            @empty
                <div class="col-12">
                    <div class="text-center p-5 bg-light rounded">
                        <i class="fas fa-search fa-3x text-muted mb-3"></i>
                        <h4>Không tìm thấy sản phẩm nào</h4>
                        <p class="text-muted">Rất tiếc, chúng tôi không tìm thấy sản phẩm nào phù hợp với từ khóa "{{ request('query') }}".</p>
                        <a href="{{ route('home') }}" class="btn btn-primary mt-3">Quay về trang chủ</a>
                    </div>
                </div>
            @endforelse
        </div>

        {{-- PHÂN TRANG --}}
        @if ($products->hasPages())
            <div class="d-flex justify-content-center mt-5">
                {{-- appends(request()->query()) để giữ lại từ khóa tìm kiếm khi chuyển trang --}}
                {{ $products->appends(request()->query())->links('pagination::bootstrap-5') }}
            </div>
        @endif

    </div>
</div>
@endsection


@push('styles')
<style>
    .search-title {
        color: #333;
        font-weight: 600;
    }
    .search-subtitle {
        font-size: 1.1rem;
        color: #666;
    }
    .product-list {
        row-gap: 25px;
    }
    /* Ghi đè CSS cho product-card để phù hợp với trang tìm kiếm (nếu cần) */
    .product-card {
        background: #fff;
        border-radius: 15px;
        overflow: hidden;
        margin-bottom: 0; /* Bỏ margin bottom vì đã có row-gap */
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        transition: all 0.3s ease;
        height: 100%;
    }
    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    }
    .product-image-container {
        position: relative;
    }
    .product-brand-icon {
        position: absolute; top: 10px; left: 10px; width: 40px; height: 40px;
    }
    .product-info { padding: 15px; text-align: center; }
    .product-name a {
        font-size: 16px; font-weight: 600; color: #333; text-decoration: none;
        height: 48px; overflow: hidden; display: -webkit-box;
        -webkit-line-clamp: 2; -webkit-box-orient: vertical;
    }
    .product-price {
        font-size: 18px; font-weight: bold; color: #ea73ac; margin: 10px 0;
    }
    .product-sizes { display: flex; justify-content: center; gap: 5px; flex-wrap: wrap; min-height: 26px; }
    .product-sizes span {
        background-color: #f0f0f0; border: 1px solid #ddd; border-radius: 5px;
        padding: 2px 8px; font-size: 12px; color: #666;
    }
</style>
@endpush