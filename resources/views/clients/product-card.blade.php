{{-- File: resources/views/clients/product-card-v2.blade.php --}}
<div class="product-card position-relative">
    <div class="product-image-container position-relative">
        <form action="{{ route('wishlist.add') }}" method="POST" class="wishlist-button">
            @csrf
            <input type="hidden" name="product_id" value="{{ $product->id }}">
            <button type="submit" class="btn btn-light btn-sm rounded-circle shadow-sm" style="position: absolute; top: 10px; right: 10px; z-index: 10;">
                <i class="fa fa-heart text-danger"></i>
            </button>
        </form>

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
        @if($product->variants->isNotEmpty())
            <div class="product-sizes">
                @foreach($product->variants as $variant)
                    @if($loop->index < 4) 
                        <span>{{ $variant->name }}</span>
                    @endif
                @endforeach
            </div>
        @endif
    </div>
</div>
