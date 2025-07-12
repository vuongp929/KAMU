@props(['product'])

{{-- Component này sử dụng cấu trúc class của template gốc "metronic shop ui" --}}
<div class="product-item">
    <div class="pi-img-wrapper">
        <img src="{{ $product->thumbnail_url }}" class="img-responsive" alt="{{ $product->name }}">
        <div>
            <a href="{{ $product->thumbnail_url }}" class="btn btn-default fancybox-fast-view">Phóng to</a>
            {{-- Nút xem nhanh có thể cần JavaScript riêng để hoạt động --}}
            <a href="#product-pop-up" class="btn btn-default fancybox-fast-view">Xem nhanh</a>
        </div>
    </div>
    <h3><a href="#">{{ $product->name }}</a></h3>
    <div class="pi-price">{{ $product->price_range }}</div>
    <a href="javascript:;" class="btn btn-default add2cart">Thêm vào giỏ</a>
    {{-- Nếu có sao đánh giá --}}
    {{-- <div class="sticker sticker-new"></div> --}}
</div>