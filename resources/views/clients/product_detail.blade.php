@extends('layouts.client')

@section('title', $product->name)

@section('content')
<div class.="main">
    <div class="container py-5">
        <div class="row product-page">
            {{-- CỘT TRÁI - ẢNH SẢN PHẨM --}}
            <div class="col-md-6">
                <div class="product-main-image mb-3">
                    <img id="main-product-image" src="{{ $product->thumbnail_url }}" alt="{{ $product->name }}" class="img-fluid rounded shadow-sm">
                </div>
                @if($product->images->count() > 1)
                <div class="product-other-images d-flex gap-2">
                    @foreach($product->images as $image)
                        <a href="{{ Storage::url($image->image_path) }}" class="thumbnail-link">
                            <img src="{{ Storage::url($image->image_path) }}" class="img-thumbnail" alt="thumbnail" style="width: 80px; height: 80px; object-fit: cover; cursor: pointer;">
                        </a>
                    @endforeach
                </div>
                @endif
            </div>

            {{-- CỘT PHẢI - THÔNG TIN & ĐẶT HÀNG --}}
            <div class="col-md-6">
                <h1>{{ $product->name }}</h1>
                <div class="price-availability-block clearfix my-3">
                    <div class="price">
                        <strong id="product-price-display" style="font-size: 2rem; color: #ea73ac;">
                            {{ $product->price_range }}
                        </strong>
                    </div>
                </div>
                <div class="description mb-4">
                    <p>{!! $product->description !!}</p>
                </div>

                <form action="{{ route('client.cart.add') }}" method="POST" id="add-to-cart-form">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="hidden" name="variant_id" id="selected-variant-id">
                    
                    {{-- LỰA CHỌN BIẾN THỂ (SIZE/MÀU...) --}}
                    <div class="product-page-options mb-4">
                        <label class="form-label fw-bold">Chọn phân loại:</label>
                        <div class="variants-container d-flex flex-wrap gap-2">
                            @foreach($product->variants as $variant)
                                <div class="variant-option">
                                    <input type="radio" class="btn-check" name="variant_option" id="variant-{{$variant->id}}" 
                                           value="{{ $variant->id }}" 
                                           data-price="{{ $variant->price }}"
                                           data-stock="{{ $variant->stock }}">
                                    <label class="btn btn-outline-primary" for="variant-{{$variant->id}}">{{ $variant->name }}</label>
                                </div>
                            @endforeach
                        </div>
                        <small id="stock-info" class="text-muted d-block mt-2"></small>
                    </div>

                    {{-- SỐ LƯỢNG --}}
                    <div class="product-page-cart d-flex align-items-center">
                        <div class="product-quantity me-3">
                            <label for="quantity" class="form-label me-2">Số lượng:</label>
                            <input type="number" id="quantity" name="quantity" value="1" min="1" class="form-control" style="width: 80px;">
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg"><i class="fas fa-shopping-cart me-2"></i>Thêm vào giỏ</button>
                    </div>
                    <div id="add-to-cart-error" class="text-danger mt-2"></div>
                </form>
            </div>
        </div>
        
        {{-- SẢN PHẨM TƯƠNG TỰ & ĐÁNH GIÁ (GIỮ NGUYÊN) --}}
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const variantRadios = document.querySelectorAll('input[name="variant_option"]');
    const priceDisplay = document.getElementById('product-price-display');
    const stockInfo = document.getElementById('stock-info');
    const selectedVariantIdInput = document.getElementById('selected-variant-id');
    const quantityInput = document.getElementById('quantity');
    const addToCartForm = document.getElementById('add-to-cart-form');
    const addToCartError = document.getElementById('add-to-cart-error');

    const formatCurrency = (amount) => new Intl.NumberFormat('vi-VN').format(amount) + 'đ';

    // Xử lý khi người dùng chọn một biến thể
    variantRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.checked) {
                const price = this.dataset.price;
                const stock = parseInt(this.dataset.stock);
                
                // Cập nhật giá, thông tin tồn kho và hidden input
                priceDisplay.textContent = formatCurrency(price);
                stockInfo.textContent = `(Còn lại ${stock} sản phẩm)`;
                selectedVariantIdInput.value = this.value;
                quantityInput.max = stock; // Giới hạn số lượng tối đa có thể mua
                addToCartError.textContent = ''; // Xóa thông báo lỗi cũ
            }
        });
    });

    // Tự động chọn biến thể đầu tiên khi tải trang
    if (variantRadios.length > 0) {
        variantRadios[0].click();
    }

    // Kiểm tra trước khi submit form
    addToCartForm.addEventListener('submit', function(event) {
        const selectedVariant = document.querySelector('input[name="variant_option"]:checked');
        
        // 1. Kiểm tra đã chọn biến thể chưa
        if (!selectedVariant) {
            event.preventDefault(); // Ngăn form submit
            addToCartError.textContent = 'Vui lòng chọn một phân loại sản phẩm.';
            return;
        }
        
        // 2. Kiểm tra số lượng tồn kho
        const stock = parseInt(selectedVariant.dataset.stock);
        const quantity = parseInt(quantityInput.value);

        if (quantity > stock) {
            event.preventDefault(); // Ngăn form submit
            addToCartError.textContent = `Số lượng bạn chọn (${quantity}) vượt quá số lượng còn lại (${stock}).`;
            return;
        }
        
        addToCartError.textContent = '';
    });
    
    // Logic đổi ảnh chính khi click vào ảnh thumbnail
    const mainImage = document.getElementById('main-product-image');
    const thumbnailLinks = document.querySelectorAll('.thumbnail-link');
    thumbnailLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            mainImage.src = this.href;
        });
    });
});
</script>
@endpush