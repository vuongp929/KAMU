{{-- File: resources/views/clients/product-card-v2.blade.php --}}
<div class="product-card">
    <div class="product-image-container">
        <a href="{{ route('client.products.show', $product) }}">
            <img src="{{ $product->thumbnail_url }}" alt="{{ $product->name }}" class="img-responsive">
            <div class="product-brand-icon">
                <img src="https://teddy.vn/wp-content/uploads/2024/05/logo-Teddy-1.png" alt="brand icon">
            </div>
        </a>
    </div>
    <div class="product-info">
        <h4 class="product-name">
            <a href="{{ route('client.products.show', $product) }}">{{ $product->name }}</a>
        </h4>
        <div class="product-price">
            {{ $product->price_range }}
        </div>
        {{-- Hiển thị các size/biến thể --}}
        @if($product->variants->isNotEmpty())
            <div class="product-sizes">
                @foreach($product->variants as $variant)
                    {{-- Chỉ hiển thị 4 size đầu tiên để tránh quá dài --}}
                    @if($loop->index < 4) 
                        <span>{{ $variant->name }}</span>
                    @endif
                @endforeach
            </div>
        @endif
    </div>
</div>