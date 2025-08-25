<div class="product-card" style="background: #fff; border-radius: 30px; overflow: hidden; border: 2px solid rgba(139, 92, 246, 0.2); box-shadow: 0 4px 15px rgba(0,0,0,0.1); transition: all 0.3s ease; position: relative; margin-bottom: 20px;" data-product-id="{{ $product->id }}" onmouseover="this.style.border='2px solid #8b5cf6'; this.style.transform='translateY(-5px)'; this.style.boxShadow='0 8px 25px rgba(139, 92, 246, 0.3)'" onmouseout="this.style.border='2px solid rgba(139, 92, 246, 0.2)'; this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(0,0,0,0.1)'">
    <!-- Logo thương hiệu -->
    <div style="position: absolute; top: 10px; left: 10px; z-index: 10; background: rgba(255,255,255,0.9); border-radius: 50%; padding: 5px; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
        <img src="https://teddy.vn/wp-content/uploads/2024/05/logo-Teddy-1.png" alt="brand" style="width: 25px; height: 25px; object-fit: contain;">
    </div>
    
    <!-- Nút yêu thích -->
    <form action="{{ route('wishlist.add') }}" method="POST" class="wishlist-button" style="position: absolute; top: 10px; right: 10px; z-index: 10;">
        @csrf
        <input type="hidden" name="product_id" value="{{ $product->id }}">
        <button type="submit" style="background: rgba(255,255,255,0.9); border: none; border-radius: 50%; width: 35px; height: 35px; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.3s ease;">
            <i class="fa fa-heart" style="color: #ff6b6b; font-size: 16px;"></i>
        </button>
    </form>

    <!-- Hình ảnh sản phẩm -->
     <div style="background: linear-gradient(135deg, #ff9a9e 0%, #fecfef 50%, #fecfef 100%); padding: 20px; text-align: center; min-height: 200px; display: flex; align-items: center; justify-content: center; border-radius: 28px 28px 0 0;">
         <a href="{{ route('client.products.show', $product) }}" style="text-decoration: none;">
             <img src="{{ $product->thumbnail_url }}" alt="{{ $product->name }}" style="max-width: 100%; max-height: 180px; object-fit: contain; border-radius: 20px;">
         </a>
     </div>

    <!-- Thông tin sản phẩm -->
    <div style="padding: 15px;">
        <h4 style="font-size: 16px; font-weight: 600; color: #8b5cf6; margin: 0 0 8px 0; line-height: 1.3;">
            <a href="{{ route('client.products.show', $product) }}" style="text-decoration: none; color: inherit;">{{ $product->name }}</a>
        </h4>
        
        <!-- Giá sản phẩm -->
        <div class="product-price" style="font-size: 18px; font-weight: bold; color: #ff6b6b; margin-bottom: 12px;">
            @if($product->variants->isNotEmpty())
                <span class="current-price">{{ number_format($product->variants->first()->price, 0, ',', '.') }}đ</span>
            @else
                <span class="current-price">Liên hệ</span>
            @endif
        </div>
        
        <!-- Các size có sẵn -->
        @if($product->variants->isNotEmpty())
            <div class="product-sizes" style="display: flex; gap: 8px; flex-wrap: wrap;">
                @foreach($product->variants as $variant)
                    <button class="size-option" 
                             data-price="{{ $variant->price }}"
                             data-variant-id="{{ $variant->id }}"
                             style="background: {{ $loop->first ? '#8b5cf6' : '#f3f4f6' }}; 
                                    color: {{ $loop->first ? '#fff' : '#6b7280' }}; 
                                    border: none; 
                                    padding: 6px 12px; 
                                    border-radius: 25px; 
                                    font-size: 12px; 
                                    font-weight: 500; 
                                    cursor: pointer; 
                                    transition: all 0.3s ease;"
                             onclick="updatePrice(this, {{ $product->id }})">
                         {{ $variant->name }}
                     </button>
                @endforeach
            </div>
        @endif
    </div>
</div>

<script>
function updatePrice(button, productId) {
    // Tìm card sản phẩm
    const productCard = button.closest('[data-product-id="' + productId + '"]');
    const priceElement = productCard.querySelector('.current-price');
    const sizeButtons = productCard.querySelectorAll('.size-option');
    
    // Reset tất cả button về trạng thái không active
    sizeButtons.forEach(btn => {
        btn.style.background = '#f3f4f6';
        btn.style.color = '#6b7280';
    });
    
    // Active button được chọn
    button.style.background = '#8b5cf6';
    button.style.color = '#fff';
    
    // Cập nhật giá
    const newPrice = button.getAttribute('data-price');
    priceElement.textContent = new Intl.NumberFormat('vi-VN').format(newPrice) + 'đ';
}
</script>
