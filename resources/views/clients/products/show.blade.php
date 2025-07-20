@extends('layouts.client')

@section('title', $product->name)

@section('content')
<div class="main product-detail-page">
    <div class="container">
        {{-- SECTION 1: THÔNG TIN CHÍNH CỦA SẢN PHẨM --}}
        <div class="row product-main-info">
            {{-- CỘT TRÁI: THƯ VIỆN ẢNH --}}
            <div class="col-md-5">
                <div class="product-gallery">
                    <div class="main-image-container">
                        {{-- Ảnh chính sẽ được cập nhật bằng JS khi click vào thumbnail --}}
                        <img id="main-product-image" src="{{ $product->thumbnail_url }}" alt="{{ $product->name }}" class="img-responsive">
                    </div>
                    {{-- Danh sách ảnh thumbnail --}}
                    @if($product->images->count() > 1)
                    <div class="thumbnail-list">
                        @foreach($product->images as $image)
                        <div class="thumbnail-item">
                            <img src="{{ Storage::url($image->image_path) }}" 
                                 data-large-src="{{ Storage::url($image->image_path) }}" 
                                 alt="Thumbnail {{ $loop->iteration }}" class="img-responsive">
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>

            {{-- CỘT PHẢI: CHI TIẾT VÀ MUA HÀNG --}}
            <div class="col-md-7">
                <div class="product-details">
                    <h1 class="product-title">{{ $product->name }}</h1>
                    
                    {{-- Phần chọn size và giá --}}
                    <div class="variant-selector">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Size</th>
                                    <th class="text-right">Giá bán</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($product->variants as $variant)
                                <tr class="variant-row" data-price="{{ $variant->price }}">
                                    <td>{{ $variant->name }}</td>
                                    <td class="text-right">{{ number_format($variant->price, 0, ',', '.') }}đ</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Giá hiển thị động --}}
                    <div class="price-display">
                        <span id="dynamic-price">{{ number_format($product->variants->first()->price ?? 0, 0, ',', '.') }}đ</span>
                    </div>

                    {{-- Các nút bấm mua hàng --}}
                    <div class="action-buttons">
                        <div class="size-tags">
                            @foreach($product->variants as $variant)
                            <button class="btn btn-size" data-variant-price="{{ $variant->price }}">{{ $variant->name }}</button>
                            @endforeach
                        </div>
                        <button class="btn btn-add-to-cart">MUA HÀNG</button>
                        <div class="quick-buy">
                            <button class="btn btn-quick-buy">ĐẶT HÀNG NHANH</button>
                            <input type="text" class="form-control" placeholder="Nhập số điện thoại">
                        </div>
                    </div>

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

        {{-- SECTION 2: MÔ TẢ CHI TIẾT VÀ HƯỚNG DẪN --}}
        <div class="row product-description-section">
            <div class="col-md-12">
                <div class="tabs-container">
                    <ul class="nav nav-tabs" role="tablist">
                        <li role="presentation" class="active"><a href="#info" aria-controls="info" role="tab" data-toggle="tab">THÔNG TIN SẢN PHẨM</a></li>
                        <li role="presentation"><a href="#guide" aria-controls="guide" role="tab" data-toggle="tab">HƯỚNG DẪN MUA HÀNG</a></li>
                    </ul>
                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane active" id="info">
                            {!! $product->description !!}
                            <p><strong>Mã sản phẩm:</strong> {{ $product->code }}</p>
                            <p><strong>Kích thước:</strong></p>
                            <ul>
                                @foreach($product->variants as $variant)
                                <li>{{ $variant->name }}: {{ number_format($variant->price, 0, ',', '.') }}đ</li>
                                @endforeach
                            </ul>
                        </div>
                        <div role="tabpanel" class="tab-pane" id="guide">
                            <p>Nội dung hướng dẫn mua hàng của bạn ở đây...</p>
                        </div>
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
                    @include('clients.product-card-v2', ['product' => $relatedProduct])
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
$(document).ready(function() {
    // 1. Logic cho thư viện ảnh (gallery)
    const mainImage = $('#main-product-image');
    const thumbnails = $('.thumbnail-item');

    thumbnails.first().addClass('active'); // Kích hoạt ảnh đầu tiên

    thumbnails.on('click', function() {
        // Lấy đường dẫn ảnh lớn từ data attribute
        const largeSrc = $(this).find('img').data('large-src');
        
        // Thay đổi ảnh chính
        mainImage.attr('src', largeSrc);
        
        // Cập nhật trạng thái active
        thumbnails.removeClass('active');
        $(this).addClass('active');
    });

    // 2. Logic cập nhật giá khi chọn size
    const priceDisplay = $('#dynamic-price');
    const sizeButtons = $('.btn-size');

    sizeButtons.first().addClass('active'); // Kích hoạt size đầu tiên

    sizeButtons.on('click', function() {
        const newPrice = $(this).data('variant-price');
        
        // Định dạng lại giá tiền
        const formattedPrice = new Intl.NumberFormat('vi-VN').format(newPrice) + 'đ';
        
        // Cập nhật giá hiển thị
        priceDisplay.text(formattedPrice);

        // Cập nhật trạng thái active
        sizeButtons.removeClass('active');
        $(this).addClass('active');
    });
});
</script>
@endpush