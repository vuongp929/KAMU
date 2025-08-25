@extends('layouts.client')

@section('title', 'Tất cả sản phẩm')

@section('content')
<div class="container mt-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
            <li class="breadcrumb-item active" aria-current="page">Tất cả sản phẩm</li>
        </ol>
    </nav>

    <div class="row">
        <!-- Sidebar Filter -->
        <div class="col-lg-3 col-md-4">
            <div class="filter-sidebar">
                <h5 class="mb-3">Bộ lọc sản phẩm</h5>
                
                <form method="GET" action="{{ route('client.products.index') }}" id="filterForm">
                    <!-- Giữ lại từ khóa tìm kiếm -->
                    @if(request('search'))
                        <input type="hidden" name="search" value="{{ request('search') }}">
                    @endif
                    
                    <!-- Lọc theo danh mục -->
                    <div class="filter-group mb-4">
                        <h6>Danh mục</h6>
                        <select name="category" class="form-select" onchange="submitFilter()">
                            <option value="">Tất cả danh mục</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Lọc theo giá -->
                    <div class="filter-group mb-4">
                        <h6>Khoảng giá</h6>
                        <div class="row">
                            <div class="col-6">
                                <input type="number" name="min_price" class="form-control" placeholder="Giá từ" value="{{ request('min_price') }}" onchange="submitFilter()">
                            </div>
                            <div class="col-6">
                                <input type="number" name="max_price" class="form-control" placeholder="Giá đến" value="{{ request('max_price') }}" onchange="submitFilter()">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Sắp xếp -->
                    <div class="filter-group mb-4">
                        <h6>Sắp xếp theo</h6>
                        <select name="sort" class="form-select" onchange="submitFilter()">
                            <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Mới nhất</option>
                            <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>Phổ biến</option>
                            <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Giá: Thấp đến cao</option>
                            <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Giá: Cao đến thấp</option>
                            <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Tên: A-Z</option>
                            <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Tên: Z-A</option>
                        </select>
                    </div>
                    
                    <!-- Nút reset filter -->
                    <div class="filter-group">
                        <a href="{{ route('client.products.index') }}" class="btn btn-outline-secondary w-100">Xóa bộ lọc</a>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Products Grid -->
        <div class="col-lg-9 col-md-8">
            <!-- Search Results Info -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4>Tất cả sản phẩm</h4>
                    @if(request('search'))
                        <p class="text-muted">Kết quả tìm kiếm cho: "{{ request('search') }}"</p>
                    @endif
                    <small class="text-muted">Hiển thị {{ $products->count() }} trong tổng số {{ $products->total() }} sản phẩm</small>
                </div>
            </div>
            
            <!-- Products Grid -->
            @if($products->count() > 0)
                <div class="row">
                    @foreach($products as $product)
                        <div class="col-lg-4 col-md-6 col-sm-6 mb-4">
                            <div class="product-card h-100">
                                <div class="product-image">
                                    <a href="{{ route('client.products.show', $product) }}">
                                        @if($product->mainImage)
                                            <img src="{{ asset('storage/' . $product->mainImage->image_path) }}" 
                                                 alt="{{ $product->name }}" 
                                                 class="img-fluid product-img">
                                        @elseif($product->firstImage)
                                            <img src="{{ asset('storage/' . $product->firstImage->image_path) }}" 
                                                 alt="{{ $product->name }}" 
                                                 class="img-fluid product-img">
                                        @else
                                            <img src="{{ asset('images/no-image.png') }}" 
                                                 alt="{{ $product->name }}" 
                                                 class="img-fluid product-img">
                                        @endif
                                    </a>
                                </div>
                                
                                <div class="product-info p-3">
                                    <h6 class="product-title">
                                        <a href="{{ route('client.products.show', $product) }}" class="text-decoration-none">
                                            {{ $product->name }}
                                        </a>
                                    </h6>
                                    
                                    @if($product->variants->count() > 0)
                                        <div class="product-price">
                                            @php
                                                $minPrice = $product->variants->min('price');
                                                $maxPrice = $product->variants->max('price');
                                            @endphp
                                            
                                            @if($minPrice == $maxPrice)
                                                <span class="price">{{ number_format($minPrice, 0, ',', '.') }}đ</span>
                                            @else
                                                <span class="price">{{ number_format($minPrice, 0, ',', '.') }}đ - {{ number_format($maxPrice, 0, ',', '.') }}đ</span>
                                            @endif
                                        </div>
                                    @endif
                                    
                                    <div class="product-categories mt-2">
                                        @foreach($product->categories->take(2) as $category)
                                            <span class="badge bg-light text-dark me-1">{{ $category->name }}</span>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $products->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <h5>Không tìm thấy sản phẩm nào</h5>
                    <p class="text-muted">Hãy thử thay đổi bộ lọc hoặc từ khóa tìm kiếm</p>
                    <a href="{{ route('client.products.index') }}" class="btn btn-primary">Xem tất cả sản phẩm</a>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.filter-sidebar {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 20px;
}

.filter-group h6 {
    font-weight: 600;
    margin-bottom: 10px;
    color: #333;
}

.product-card {
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    transition: all 0.3s ease;
    background: white;
}

.product-card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    transform: translateY(-2px);
}

.product-image {
    position: relative;
    overflow: hidden;
    border-radius: 8px 8px 0 0;
}

.product-img {
    width: 100%;
    height: 250px;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.product-card:hover .product-img {
    transform: scale(1.05);
}

.product-title a {
    color: #333;
    font-weight: 500;
}

.product-title a:hover {
    color: #007bff;
}

.product-price .price {
    font-weight: 600;
    color: #e74c3c;
    font-size: 1.1em;
}

.breadcrumb {
    background: none;
    padding: 0;
}

.breadcrumb-item a {
    color: #007bff;
    text-decoration: none;
}

@media (max-width: 768px) {
    .filter-sidebar {
        margin-bottom: 30px;
    }
    
    .product-img {
        height: 200px;
    }
}
</style>

<script>
function submitFilter() {
    document.getElementById('filterForm').submit();
}
</script>
@endsection