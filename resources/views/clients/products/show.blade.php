@extends('layouts.client')

@section('title', $product->name)

@push('styles')
<link rel="stylesheet" href="{{ asset('css/modern-product.css') }}">
@endpush

@section('content')
<div class="modern-product-page">
    <div class="container">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Product Gallery -->
            <div class="space-y-4">
                <div class="aspect-square rounded-lg overflow-hidden bg-card relative">
                    <img id="main-product-image" src="{{ $product->thumbnail_url }}" alt="{{ $product->name }}" class="w-full h-full object-cover" />
                    <div class="absolute top-4 left-4 space-x-2">
                        <span class="badge badge-new">Mới</span>
                        <span class="badge badge-hot">Hot</span>
                    </div>
                </div>
                {{-- Danh sách ảnh thumbnail --}}
                @if($product->images->count() > 1)
                <div class="grid grid-cols-4 gap-3">
                    @foreach($product->images as $image)
                    <div class="aspect-square rounded-lg overflow-hidden image-thumbnail {{ $loop->first ? 'active' : '' }} cursor-pointer" onclick="changeMainImage('{{ Storage::url($image->image_path) }}')">
                        <img src="{{ Storage::url($image->image_path) }}" 
                             data-large-src="{{ Storage::url($image->image_path) }}" 
                             alt="Thumbnail {{ $loop->iteration }}" class="w-full h-full object-cover" />
                    </div>
                    @endforeach
                </div>
                @endif
            </div>

            <!-- Product Info -->
            <div class="space-y-6">
                <div class="space-y-4">
                    <div class="flex items-center gap-2">
                        @foreach($product->categories->take(2) as $category)
                            <span class="badge badge-hot">{{ $category->name }}</span>
                        @endforeach
                    </div>
                    <h1 class="text-3xl font-bold text-foreground">{{ $product->name }}</h1>
                    <div class="flex items-center gap-3">
                        @php
                            $avgStars = $product->reviews()->where('is_hidden', false)->avg('stars');
                            $reviewCount = $product->reviews()->where('is_hidden', false)->count();
                        @endphp
                        <div class="flex items-center gap-1">
                            @for($i=1;$i<=5;$i++)
                                <svg class="star {{ $i <= round($avgStars) ? '' : 'star-empty' }}" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            @endfor
                        </div>
                        <span class="text-sm text-muted-foreground">({{ $reviewCount }} đánh giá)</span>
                    </div>
                </div>
                    
                {{-- Phần chọn size và giá --}}
                <div class="card p-6 space-y-6">
                    <div class="flex items-baseline gap-3">
                        <span class="text-3xl font-bold text-primary" id="dynamic-price">{{ number_format($product->variants->first()->price ?? 0, 0, ',', '.') }}đ</span>
                    </div>
                        
                    <div class="space-y-4">
                        <h4 class="font-semibold text-foreground">Chọn phiên bản:</h4>
                        <div class="space-y-3">
                            @if($product->variants->isNotEmpty())
                                @foreach($product->variants as $variant)
                                    <div class="size-option {{ $loop->first ? 'active' : '' }} {{ $variant->stock == 0 ? 'disabled' : '' }}" 
                                         data-variant-id="{{ $variant->id }}"
                                         data-price="{{ $variant->price }}"
                                         data-stock="{{ $variant->stock }}">
                                        <div class="flex items-center justify-between">
                                            <span class="size-name">{{ $variant->name }}</span>
                                            <div class="flex items-center gap-2">
                                                <span class="size-price">{{ number_format($variant->price, 0, ',', '.') }}đ</span>
                                                @if($variant->stock == 0)
                                                    <span class="stock-badge">Hết hàng</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <p class="no-variants">Sản phẩm hiện chưa có phiên bản nào.</p>
                            @endif
                        </div>
                    </div>
                    </div>

                    {{-- Form mua hàng hiện đại --}}
                    <form action="{{ route('client.cart.add') }}" method="POST" class="purchase-form">
                        @csrf
                        <input type="hidden" name="variant_id" id="selected-variant-id" value="{{ $product->variants->first()->id ?? '' }}">
                        
                        <div class="stock-info" id="stock-display">
                            @if($product->variants->first() && $product->variants->first()->stock > 0)
                                <div class="stock-available">
                                    <svg class="icon icon-check" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <span>Còn {{ $product->variants->first()->stock }} sản phẩm</span>
                                </div>
                            @else
                                <div class="stock-unavailable">
                                    <svg class="icon icon-times" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                    </svg>
                                    <span>Hết hàng</span>
                                </div>
                            @endif
                        </div>

                        <div class="space-y-4">
                            <h4 class="font-semibold text-foreground">Số lượng:</h4>
                            <div class="quantity-control">
                                <button type="button" class="btn btn-outline" onclick="changeQuantity(-1)">−</button>
                                <input id="product-quantity" type="number" name="quantity" value="1" min="1" class="quantity-display" readonly>
                                <button type="button" class="btn btn-outline" onclick="changeQuantity(1)">+</button>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <button class="btn btn-primary btn-lg w-full" type="submit">
                                <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.5 6M7 13l-1.5-6m0 0h12.5M17 13v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6"/>
                                </svg>
                                <span>Thêm vào giỏ hàng</span>
                                <div class="btn-ripple"></div>
                            </button>
                            <button class="btn btn-outline btn-lg w-full" type="button">
                                <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                                <span>Mua ngay</span>
                            </button>
                        </div>
                    </form>

                    {{-- Thông tin thêm và chính sách --}}
                    <div class="extra-info">
                        <ul>
                            <li><i class="fa fa-check-circle"></i> Giao Hàng Nội Thành Siêu Tốc - Giao Đúng Giờ & Tận Tay</li>
                            <li><i class="fa fa-check-circle"></i> Giao Hàng Toàn Quốc 2 - 5 Ngày - Nhận Hàng Mới Phải Trả Tiền</li>
                            <li><i class="fa fa-check-circle"></i> Bảo Hành Đường Chỉ Vĩnh Viễn - Bảo Hành Bông Gấu 1 Năm</li>
                            <li><i class="fa fa-check-circle"></i> Địa Chỉ Shop Dễ Tìm - Có Chỗ Để Xe Ô Tô Miễn Phí</li>
                        </ul>
                    </div>

                    {{-- Thông tin liên hệ --}}
                    <div class="contact-info">
                        <span><i class="fa fa-map-marker"></i> 388 Xã Đàn, Đống Đa, Hà Nội</span>
                        <span><i class="fa fa-phone"></i> 096.5555.346 - 096.2222.346</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- SECTION 2: THÔNG TIN CHI TIẾT VÀ ĐÁNH GIÁ --}}
        <div class="mt-12">
            <div class="tabs">
                <div class="tabs-list">
                    <button class="tab-trigger active" data-tab="description">Mô tả sản phẩm</button>
                    <button class="tab-trigger" data-tab="specifications">Thông số kỹ thuật</button>
                    <button class="tab-trigger" data-tab="reviews">Đánh giá ({{ $product->reviews()->where('is_hidden', false)->count() }})</button>
                </div>
                
                <div class="tab-content active" id="description">
                    <div class="prose">
                        <h3 class="text-2xl font-bold mb-4">Mô tả sản phẩm</h3>
                        <div class="text-muted-foreground">
                            {!! $product->description !!}
                        </div>
                        <div class="mt-6 space-y-2">
                            <p><strong>Mã sản phẩm:</strong> {{ $product->code }}</p>
                            <p><strong>Các phiên bản:</strong></p>
                            <ul class="list-disc list-inside space-y-1">
                                @foreach($product->variants as $variant)
                                <li>{{ $variant->name }}: {{ number_format($variant->price, 0, ',', '.') }}đ</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
                
                <div class="tab-content" id="specifications">
                    <div class="prose">
                        <h3 class="text-2xl font-bold mb-4">Thông số kỹ thuật</h3>
                        <div class="space-y-4">
                            <div class="flex justify-between py-2 border-b border-gray-200">
                                <span class="font-medium">Thương hiệu:</span>
                                <span class="text-muted-foreground">{{ $product->brand ?? 'Chưa cập nhật' }}</span>
                            </div>
                            <div class="flex justify-between py-2 border-b border-gray-200">
                                <span class="font-medium">Danh mục:</span>
                                <span class="text-muted-foreground">{{ $product->categories->first()->name ?? 'Chưa phân loại' }}</span>
                            </div>
                            <div class="flex justify-between py-2 border-b border-gray-200">
                                <span class="font-medium">Mã sản phẩm:</span>
                                <span class="text-muted-foreground">{{ $product->code }}</span>
                            </div>
                            <div class="flex justify-between py-2 border-b border-gray-200">
                                <span class="font-medium">Số lượng phiên bản:</span>
                                <span class="text-muted-foreground">{{ $product->variants->count() }} phiên bản</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="tab-content" id="reviews">
                    <div class="space-y-6">
                        <div class="flex items-center justify-between">
                            <h3 class="text-2xl font-bold">Đánh giá từ khách hàng</h3>
                            <div class="flex items-center gap-3">
                                @php
                                    $avgStars = $product->reviews()->where('is_hidden', false)->avg('stars');
                                    $reviewCount = $product->reviews()->where('is_hidden', false)->count();
                                @endphp
                                <span class="text-3xl font-bold text-primary">{{ number_format($avgStars, 1) ?: '0.0' }}</span>
                                <div class="flex items-center gap-1">
                                    @for($i = 1; $i <= 5; $i++)
                                        <svg class="star {{ $i <= round($avgStars) ? '' : 'star-empty' }}" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    @endfor
                                </div>
                                <span class="text-sm text-muted-foreground">({{ $reviewCount }} đánh giá)</span>
                            </div>
                        </div>
                        
                        <div class="review-list">
                            @php
                                $reviews = $product->reviews()->where('is_hidden', false)->whereNull('parent_id')->latest()->take(5)->get();
                            @endphp
                            @forelse($reviews as $review)
                                <div class="review-item">
                                    <div class="review-header">
                                        <div class="review-avatar">{{ mb_substr($review->user->name ?? 'Ẩn', 0, 1) }}</div>
                                        <div class="review-meta">
                                            <span class="review-name">{{ $review->user->name ?? 'Ẩn' }}</span>
                                            <span class="review-stars">
                                                @if($review->stars)
                                                    @for($i=1;$i<=5;$i++)
                                                        @if($i <= $review->stars)
                                                            <i class="fa fa-star"></i>
                                                        @else
                                                            <i class="fa fa-star-o"></i>
                                                        @endif
                                                    @endfor
                                                @endif
                                            </span>
                                        </div>
                                        <span class="review-time">{{ $review->created_at->format('d/m/Y H:i') }}</span>
                                        @auth
                                            @if($review->user_id === auth()->id())
                                                <button class="btn btn-danger btn-sm ml-2 delete-review-btn" data-id="{{ $review->id }}">Xóa</button>
                                            @endif
                                        @endauth
                                    </div>
                                    <div class="review-content">{{ $review->content }}</div>
                                    <div class="review-actions">
                                        @auth
                                        <form action="{{ route('products.reviews.reply', [$product->id, $review->id]) }}" method="POST" style="flex:1;">
                                            @csrf
                                            <textarea name="content" class="form-control mb-1" rows="1" placeholder="Trả lời bình luận này..." required style="border-radius:8px;"></textarea>
                                            <button type="submit" class="btn btn-sm btn-outline-secondary">Trả lời</button>
                                        </form>
                                        @endauth
                                    </div>
                                    @if($review->replies()->where('is_hidden', false)->count())
                                    <div class="reply-list">
                                        @foreach($review->replies()->where('is_hidden', false)->get() as $reply)
                                            <div class="reply-item">
                                                <div class="reply-header">
                                                    <div class="reply-avatar">{{ mb_substr($reply->user->name ?? 'Ẩn', 0, 1) }}</div>
                                                    <span class="reply-name">{{ $reply->user->name ?? 'Ẩn' }}</span>
                                                    <span class="reply-time">{{ $reply->created_at->format('d/m/Y H:i') }}</span>
                                                </div>
                                                <div class="reply-content">{{ $reply->content }}</div>
                                            </div>
                                        @endforeach
                                    </div>
                                    @endif
                                </div>
                            @empty
                                <div class="text-center text-muted-foreground py-8">
                                    <p>Chưa có đánh giá nào cho sản phẩm này.</p>
                                </div>
                            @endforelse
                        </div>
                        
                        {{-- Nút mở popup đánh giá --}}
                        @auth
                            @if($canReview)
                                <button type="button" class="btn btn-success mt-4" id="openReviewPopupBtn">
                                    <i class="fa fa-pencil"></i> Viết đánh giá
                                </button>
                            @else
                                <div class="mt-4 text-info" style="font-size:1.08rem;">
                                    <i class="fa fa-info-circle"></i> Chỉ khách đã mua sản phẩm mới được đánh giá.
                                </div>
                            @endif
                        @endauth
                    </div>
                </div>
            </div>
        </div>



        {{-- SECTION 3: SẢN PHẨM TƯƠNG TỰ --}}
        <div class="row product-section">
            <div class="col-md-12 text-center">
                <div class="section-title">
                    <span>SẢN PHẨM TƯƠNG TỰ</span>
                </div>
            </div>
            @forelse($relatedProducts as $relatedProduct)
                <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
                    @include('clients.product-card', ['product' => $relatedProduct])
                </div>
            @empty
                <p class="text-center col-xs-12">Không có sản phẩm nào tương tự.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
// Modern Product Page JavaScript

// Quantity controls
function changeQuantity(delta) {
    const input = document.getElementById('product-quantity');
    const currentValue = parseInt(input.value) || 1;
    const newValue = Math.max(1, currentValue + delta);
    
    // Get max stock from selected variant
    const selectedVariant = document.querySelector('.size-option.active');
    const maxStock = selectedVariant ? parseInt(selectedVariant.dataset.stock) : 999;
    
    if (newValue <= maxStock) {
        input.value = newValue;
        
        // Add animation effect
        input.style.transform = 'scale(1.1)';
        setTimeout(() => {
            input.style.transform = 'scale(1)';
        }, 150);
    }
}

// Size selection
document.addEventListener('DOMContentLoaded', function() {
    const sizeOptions = document.querySelectorAll('.size-option');
    const priceDisplay = document.getElementById('dynamic-price');
    const stockDisplay = document.getElementById('stock-display');
    const selectedVariantInput = document.getElementById('selected-variant-id');
    
    sizeOptions.forEach(option => {
        option.addEventListener('click', function() {
            if (this.classList.contains('disabled')) return;
            
            // Remove active class from all options
            sizeOptions.forEach(opt => opt.classList.remove('active'));
            
            // Add active class to clicked option
            this.classList.add('active');
            
            // Update hidden input
            selectedVariantInput.value = this.dataset.variantId;
            
            // Update price with animation
            const price = this.dataset.price;
            priceDisplay.style.transform = 'scale(0.9)';
            priceDisplay.style.opacity = '0.5';
            
            setTimeout(() => {
                priceDisplay.textContent = new Intl.NumberFormat('vi-VN').format(price) + 'đ';
                priceDisplay.style.transform = 'scale(1)';
                priceDisplay.style.opacity = '1';
            }, 150);
            
            // Update stock info
            const stock = parseInt(this.dataset.stock);
            const stockContainer = stockDisplay.querySelector('.stock-available, .stock-unavailable');
            
            if (stock > 0) {
                stockDisplay.innerHTML = `
                    <div class="stock-available">
                        <i class="icon-check">✓</i>
                        <span>Còn ${stock} sản phẩm</span>
                    </div>
                `;
            } else {
                stockDisplay.innerHTML = `
                    <div class="stock-unavailable">
                        <i class="icon-times">✕</i>
                        <span>Hết hàng</span>
                    </div>
                `;
            }
            
            // Reset quantity if exceeds new stock
            const quantityInput = document.getElementById('product-quantity');
            if (parseInt(quantityInput.value) > stock) {
                quantityInput.value = Math.min(stock, 1);
            }
        });
    });
    
    // Initialize first option as active
    if (sizeOptions.length > 0) {
        sizeOptions[0].click();
    }
    
    // Initialize tabs
    initTabs();
});

// Image gallery functionality
function changeMainImage(src, element) {
    const mainImage = document.getElementById('main-product-image');
    const thumbnails = document.querySelectorAll('.image-thumbnail');
    
    // Remove active class from all thumbnails
    thumbnails.forEach(thumb => thumb.classList.remove('active'));
    
    // Add active class to clicked thumbnail
    if (element) {
        element.classList.add('active');
    }
    
    // Change main image with fade effect
    mainImage.style.opacity = '0';
    setTimeout(() => {
        mainImage.src = src;
        mainImage.style.opacity = '1';
    }, 200);
}

// Tab switching functionality
function initTabs() {
    const tabTriggers = document.querySelectorAll('.tab-trigger');
    const tabContents = document.querySelectorAll('.tab-content');
    
    tabTriggers.forEach(trigger => {
        trigger.addEventListener('click', function() {
            const targetTab = this.dataset.tab;
            
            // Remove active from all triggers and contents
            tabTriggers.forEach(t => t.classList.remove('active'));
            tabContents.forEach(c => c.classList.remove('active'));
            
            // Add active to clicked trigger and corresponding content
            this.classList.add('active');
            const targetContent = document.getElementById(targetTab);
            if (targetContent) {
                targetContent.classList.add('active');
            }
        });
    });
}

// Button ripple effect
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('btn-add-cart')) {
        const button = e.target;
        const ripple = button.querySelector('.btn-ripple');
        
        const rect = button.getBoundingClientRect();
        const size = Math.max(rect.width, rect.height);
        const x = e.clientX - rect.left - size / 2;
        const y = e.clientY - rect.top - size / 2;
        
        ripple.style.width = ripple.style.height = size + 'px';
        ripple.style.left = x + 'px';
        ripple.style.top = y + 'px';
        
        ripple.style.transform = 'scale(0)';
        ripple.style.opacity = '1';
        
        setTimeout(() => {
            ripple.style.transform = 'scale(4)';
            ripple.style.opacity = '0';
        }, 10);
    }
});

// Smooth scroll animations
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.style.opacity = '1';
            entry.target.style.transform = 'translateY(0)';
        }
    });
}, observerOptions);

// Observe elements for animation
document.addEventListener('DOMContentLoaded', function() {
    const animatedElements = document.querySelectorAll('.product-gallery-modern, .product-info-modern');
    
    animatedElements.forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(30px)';
        el.style.transition = 'all 0.6s cubic-bezier(0.25, 0.46, 0.45, 0.94)';
        observer.observe(el);
    });
    
    // Trigger animations after a short delay
    setTimeout(() => {
        animatedElements.forEach(el => {
            el.style.opacity = '1';
            el.style.transform = 'translateY(0)';
        });
    }, 100);
});

// Form validation with modern feedback
document.querySelector('.purchase-form').addEventListener('submit', function(e) {
    const selectedVariant = document.querySelector('.size-option.active');
    const quantity = parseInt(document.getElementById('product-quantity').value);
    
    if (!selectedVariant) {
        e.preventDefault();
        showNotification('Vui lòng chọn phiên bản sản phẩm', 'error');
        return;
    }
    
    const stock = parseInt(selectedVariant.dataset.stock);
    if (stock === 0) {
        e.preventDefault();
        showNotification('Sản phẩm này hiện đã hết hàng', 'error');
        return;
    }
    
    if (quantity > stock) {
        e.preventDefault();
        showNotification(`Chỉ còn ${stock} sản phẩm trong kho`, 'error');
        return;
    }
    
    // Show success animation
    const button = this.querySelector('.btn-add-cart');
    button.style.transform = 'scale(0.95)';
    setTimeout(() => {
        button.style.transform = 'scale(1)';
    }, 150);
});

// Modern notification system
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <div class="notification-content">
            <span>${message}</span>
            <button onclick="this.parentElement.parentElement.remove()">&times;</button>
        </div>
    `;
    
    // Add styles
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${type === 'error' ? '#ff6b6b' : '#00b894'};
        color: white;
        padding: 15px 20px;
        border-radius: 12px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.2);
        z-index: 1000;
        transform: translateX(100%);
        transition: transform 0.3s ease;
    `;
    
    document.body.appendChild(notification);
    
    // Animate in
    setTimeout(() => {
        notification.style.transform = 'translateX(0)';
    }, 10);
    
    // Auto remove
    setTimeout(() => {
        notification.style.transform = 'translateX(100%)';
        setTimeout(() => {
            notification.remove();
        }, 300);
    }, 3000);
}


// Popup HTML
const reviewPopupHtml = `
<div id="reviewPopupOverlay" style="position:fixed;z-index:9999;top:0;left:0;width:100vw;height:100vh;background:rgba(30,41,59,0.45);display:flex;align-items:center;justify-content:center;">
  <div id="reviewPopupCard" style="background:#fff;border-radius:20px;box-shadow:0 25px 50px -12px rgba(0,0,0,0.25);max-width:420px;width:95vw;overflow:hidden;animation:popupIn .3s cubic-bezier(.4,2,.6,1);position:relative;">
    <button id="closeReviewPopupBtn" style="position:absolute;top:12px;right:16px;background:none;border:none;font-size:1.5rem;color:#888;cursor:pointer;z-index:2;">&times;</button>
    <div style="padding:32px 24px 24px 24px;">
      <div style="display:flex;align-items:center;gap:16px;margin-bottom:18px;">
        <div style="width:48px;height:48px;border-radius:50%;background:rgba(59,130,246,0.12);display:flex;align-items:center;justify-content:center;color:#2563eb;font-size:2rem;">
          <i class="fa fa-user"></i>
        </div>
        <div>
          <div style="font-weight:700;font-size:18px;">Chia sẻ trải nghiệm của bạn</div>
          <div style="color:#64748b;font-size:13px;">Đánh giá & bình luận giúp cộng đồng</div>
        </div>
      </div>
      <div style="margin-bottom:18px;">
        <label style="font-weight:600;font-size:14px;color:#374151;">Đánh giá của bạn</label>
        <div id="popupStars" style="display:flex;gap:8px;margin:10px 0 0 0;">
          ${[1,2,3,4,5].map(i=>'<span class="popup-star" data-rating="'+i+'" style="font-size:2rem;cursor:pointer;color:#d1d5db;transition:.2s;"><i class="fa fa-star"></i></span>').join('')}
        </div>
        <div id="popupRatingBadge" style="display:none;margin-top:4px;font-size:12px;font-weight:500;color:#92400e;background:#fef3c7;border-radius:12px;padding:2px 10px;border:1px solid #fde68a;display:inline-block;"></div>
      </div>
      <div style="margin-bottom:18px;">
        <label style="font-weight:600;font-size:14px;color:#374151;">Bình luận chi tiết</label>
        <div style="position:relative;">
          <textarea id="popupComment" maxlength="500" style="width:100%;min-height:90px;padding:12px 16px;border:2px solid #e5e7eb;border-radius:12px;font-size:14px;resize:none;outline:none;transition:.2s;"></textarea>
          <div id="popupCharCounter" style="position:absolute;bottom:8px;right:14px;font-size:12px;color:#9ca3af;">0/500</div>
        </div>
      </div>
      <button id="popupSubmitBtn" class="btn btn-primary w-100" style="padding:12px 0;font-weight:500;border-radius:12px;transition:.2s;">Gửi đánh giá</button>
      <div id="popupSuccess" style="display:none;text-align:center;padding:24px 0 0 0;">
        <div style="font-size:2.5rem;color:#10b981;"><i class="fa fa-heart"></i></div>
        <div style="font-size:20px;font-weight:700;margin:8px 0 4px 0;">Cảm ơn bạn!</div>
        <div style="color:#374151;">Đánh giá của bạn đã được gửi thành công</div>
      </div>
    </div>
  </div>
</div>
<style>@keyframes popupIn{0%{transform:scale(.8);opacity:0;}100%{transform:scale(1);opacity:1;}}</style>
`;

function openReviewPopup() {
  if(document.getElementById('reviewPopupOverlay')) return;
  document.body.insertAdjacentHTML('beforeend', reviewPopupHtml);
  let rating = 0;
  const stars = document.querySelectorAll('.popup-star');
  const badge = document.getElementById('popupRatingBadge');
  const comment = document.getElementById('popupComment');
  const charCounter = document.getElementById('popupCharCounter');
  const submitBtn = document.getElementById('popupSubmitBtn');
  const success = document.getElementById('popupSuccess');
  const closeBtn = document.getElementById('closeReviewPopupBtn');
  const badges = {5:'Tuyệt vời!',4:'Rất tốt!',3:'Ổn!',2:'Tạm được',1:'Cần cải thiện'};
  function updateStars(val){
    stars.forEach((s,i)=>{s.style.color=(i<val)?'#fbbf24':'#d1d5db';});
    if(val>0){badge.textContent=badges[val];badge.style.display='inline-block';}else{badge.style.display='none';}
  }
  stars.forEach(s=>{
    s.addEventListener('mouseenter',()=>updateStars(+s.dataset.rating));
    s.addEventListener('mouseleave',()=>updateStars(rating));
    s.addEventListener('click',()=>{rating=+s.dataset.rating;updateStars(rating);});
  });
  comment.addEventListener('input',()=>{charCounter.textContent=comment.value.length+'/500';});
  submitBtn.addEventListener('click',function(){
    if(rating===0||!comment.value.trim()){submitBtn.classList.add('shake');setTimeout(()=>submitBtn.classList.remove('shake'),300);return;}
    submitBtn.disabled=true;submitBtn.textContent='Đang gửi...';
    // AJAX gửi đánh giá
    const productId = {{ $product->id }};
    const url = `/products/${productId}/reviews`;
    const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    $.ajax({
      url: url,
      method: 'POST',
      data: {stars: rating, content: comment.value},
      headers: {'X-CSRF-TOKEN': csrf},
      success: function(res){
        submitBtn.style.display='none';success.style.display='block';
        setTimeout(()=>{closeBtn.click();window.location.reload();}, 1800);
      },
      error: function(xhr){
        submitBtn.disabled=false;submitBtn.textContent='Gửi đánh giá';
        alert('Có lỗi xảy ra khi gửi đánh giá!');
      }
    });
  });
  closeBtn.addEventListener('click',()=>{
    document.getElementById('reviewPopupOverlay').remove();
  });
}
document.addEventListener('DOMContentLoaded',function(){
  const btn = document.getElementById('openReviewPopupBtn');
  if(btn) btn.addEventListener('click',openReviewPopup);
});
$(document).on('click', '.delete-review-btn', function() {
    if (!confirm('Bạn chắc chắn muốn xóa đánh giá này?')) return;
    var reviewId = $(this).data('id');
    var productId = {{ $product->id }};
    $.ajax({
        url: `/products/${productId}/reviews/${reviewId}`,
        type: 'DELETE',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        success: function(res) {
            if (res.success) location.reload();
        },
        error: function() {
            alert('Không thể xóa đánh giá!');
        }
    });
});
</script>
@endpush
@push('styles')
<style>
.review-list {
    display: flex;
    flex-direction: column;
    gap: 24px;
}
.review-item {
    background: #fff;
    border-radius: 18px;
    box-shadow: 0 4px 24px rgba(30,41,59,0.08);
    padding: 20px 24px 16px 24px;
    position: relative;
    transition: box-shadow .2s;
}
.review-item:hover {
    box-shadow: 0 8px 32px rgba(30,41,59,0.14);
}
.review-header {
    display: flex;
    align-items: center;
    gap: 14px;
    margin-bottom: 6px;
}
.review-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg,#e0e7ff 0%,#f0fdfa 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.3rem;
    color: #6366f1;
    font-weight: 700;
    box-shadow: 0 2px 8px rgba(99,102,241,0.08);
}
.review-meta {
    display: flex;
    flex-direction: column;
}
.review-name {
    font-weight: 600;
    color: #1e293b;
    font-size: 1rem;
}
.review-stars {
    color: #fbbf24;
    font-size: 1.1rem;
    letter-spacing: 1px;
}
.review-time {
    color: #94a3b8;
    font-size: 0.92rem;
    margin-left: 2px;
}
.review-content {
    color: #334155;
    font-size: 1.08rem;
    margin: 8px 0 0 0;
    word-break: break-word;
}
.review-actions {
    margin-top: 10px;
    display: flex;
    gap: 10px;
}
.reply-list {
    margin-top: 12px;
    margin-left: 48px;
    display: flex;
    flex-direction: column;
    gap: 10px;
}
.reply-item {
    background: #f8fafc;
    border-radius: 14px;
    box-shadow: 0 2px 8px rgba(30,41,59,0.04);
    padding: 12px 16px 10px 16px;
    position: relative;
}
.reply-header {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 2px;
}
.reply-avatar {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    background: linear-gradient(135deg,#f0fdfa 0%,#e0e7ff 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.95rem;
    color: #0ea5e9;
    font-weight: 700;
}
.reply-name {
    font-weight: 500;
    color: #2563eb;
    font-size: 0.98rem;
}
.reply-time {
    color: #94a3b8;
    font-size: 0.9rem;
}
.reply-content {
    color: #334155;
    font-size: 1rem;
    margin-top: 2px;
}
</style>
@endpush