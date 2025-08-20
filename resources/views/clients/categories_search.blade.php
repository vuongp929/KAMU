@extends('layouts.client')

@section('title', 'Sản phẩm thuộc danh mục: ' . $category->name)

@section('CSS')
<!-- Bạn có thể thêm CSS tùy chỉnh ở đây nếu cần -->
@endsection

@section('content')
<div class="container">
    <h2 class="my-4">Sản phẩm thuộc danh mục: "{{ $category->name }}"</h2>

    @if($products->isEmpty())
        <div class="alert alert-warning">
            Không tìm thấy sản phẩm nào thuộc danh mục "{{ $category->name }}"
        </div>
    @else
        <div class="row">
            @foreach($products as $product)
                <div class="col-md-3">
                    <a href="{{ route('product.show', $product->id) }}" class="text-decoration-none">
                    <div class="card">
                        @if($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}" class="card-img-top" alt="{{ $product->name }}">
                        @else
                            <img src="{{ asset('images/default-product.svg') }}" class="card-img-top" alt="Hình ảnh mặc định">
                        @endif
                        <div class="card-body">
                            <h5 class="card-title text-truncate" title="{{ $product->name }}">{{ $product->name }}</h5>
                            <form action="{{ route('cart.add') }}" method="POST" onsubmit="return validateSizeSelection({{ $product->id }})">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                <input type="hidden" id="selected-size-{{ $product->id }}" name="size" value="{{ $product->variants->first()->id }}">
                                <input type="hidden" name="redirect_url" value="{{ url()->current() }}">
                                <!-- Nút chọn size (giá trị từ biến thể) --> 
                                <div class="size-buttons mb-2">
                                    @foreach ($product->variants as $variant)
                                        <button type="button" class="btn btn-outline-secondary btn-sm size-button" data-size-id="{{ $variant->id }}" data-price="{{ $variant->price }}" onclick="selectSize({{ $product->id }}, this)">
                                            {{ $variant->size }}
                                        </button>
                                    @endforeach
                                </div>
                                <!-- Giá sản phẩm --> 
                                <p class="price">
                                    Giá: <span id="product-price-{{ $product->id }}">{{ number_format($product->variants->first()->price, 0, ',', '.') }} VND</span>
                                </p>
                                <button type="submit" class="btn btn-primary btn-block">Thêm vào giỏ</button>
                            </form>
                        </div>
                    </div>
                    </a>
                </div>
            @endforeach
        </div>

        <!-- Phân trang -->
        <div class="d-flex justify-content-center mt-3">
            {{ $products->links() }}
        </div>
    @endif
</div>
@endsection

@section('JS')
<script>
    function selectSize(productId, buttonElement) {
        // Loại bỏ class 'selected' ở tất cả các nút size của sản phẩm này
        const sizeButtons = buttonElement.parentElement.querySelectorAll('.size-button');
        sizeButtons.forEach(button => button.classList.remove('selected'));
        // Thêm class 'selected' cho nút được chọn
        buttonElement.classList.add('selected');
        
        // Cập nhật giá sản phẩm hiển thị theo size được chọn
        const price = buttonElement.getAttribute('data-price');
        document.getElementById('product-price-' + productId).textContent = new Intl.NumberFormat().format(price) + ' VND';
        
        // Cập nhật giá trị của input ẩn chứa id của biến thể đã chọn
        const sizeId = buttonElement.getAttribute('data-size-id');
        document.getElementById('selected-size-' + productId).value = sizeId;
    }

    function validateSizeSelection(productId) {
        const selectedSize = document.getElementById('selected-size-' + productId).value;
        if (!selectedSize) {
            alert('Vui lòng chọn kích cỡ sản phẩm trước khi thêm vào giỏ hàng!');
            return false;
        }
        return true;
    }
</script>
@endsection
