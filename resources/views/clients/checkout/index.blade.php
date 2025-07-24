@extends('layouts.client')

@section('title', 'Thanh toán đơn hàng')

@push('styles')
<style>
    /* CSS tùy chỉnh chỉ dành cho trang checkout */
    .checkout-page {
        background-color: #f9f9f9;
        padding: 40px 0;
    }
    .checkout-form h4, .order-summary h4 {
        font-weight: 600;
        color: #333;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 1px solid #eee;
    }
    .form-control {
        border-radius: 8px;
        padding: 10px 15px;
    }
    .order-summary {
        background-color: #fff;
        border-radius: 10px;
        padding: 25px;
        border: 1px solid #eee;
    }
    .order-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
        padding-bottom: 15px;
        border-bottom: 1px solid #f5f5f5;
    }
    .order-item:last-child {
        border-bottom: none;
        margin-bottom: 0;
        padding-bottom: 0;
    }
    .order-item .item-details {
        display: flex;
        align-items: center;
    }
    .order-item .item-image {
        position: relative;
        margin-right: 15px;
    }
    .order-item .item-image img {
        width: 65px;
        height: 65px;
        object-fit: cover;
        border-radius: 8px;
    }
    .order-item .item-quantity {
        position: absolute;
        top: -8px;
        right: -8px;
        background-color: #8357ae;
        color: white;
        font-size: 12px;
        font-weight: bold;
        width: 22px;
        height: 22px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 2px solid white;
    }
    .item-info .product-name {
        font-weight: 600;
        color: #333;
    }
    .item-info .variant-name {
        color: #777;
        font-size: 14px;
    }
    .item-price {
        font-weight: 600;
        color: #555;
    }
    .summary-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
    }
    .summary-total {
        font-size: 1.2rem;
        font-weight: bold;
    }
    .payment-methods .form-check {
        padding: 15px;
        border: 1px solid #ddd;
        border-radius: 8px;
        margin-bottom: 10px;
    }
    .btn-place-order {
        background-color: #ea73ac;
        color: white;
        font-weight: bold;
        padding: 12px;
        font-size: 1.1rem;
        border: none;
    }
    .btn-place-order:hover {
        background-color: #d66095;
        color: white;
    }
</style>
@endpush

@section('content')
<div class="checkout-page">
    <div class="container">
        <div class="text-center mb-5">
            <h1>Thanh toán</h1>
        </div>
        
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>@foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach</ul>
            </div>
        @endif

        <form action="{{ route('client.checkout.placeOrder') }}" method="POST">
            @csrf
            <div class="row">
                {{-- CỘT TRÁI: THÔNG TIN GIAO HÀNG --}}
                <div class="col-lg-7 checkout-form">
                    <h4>Thông tin giao hàng</h4>
                    @guest
                        <p>Bạn đã có tài khoản? <a href="{{ route('login') }}">Đăng nhập</a> để điền thông tin nhanh hơn.</p>
                    @endguest
                    
                    <div class="mb-3">
                        <input type="text" name="name" class="form-control" placeholder="Họ và tên *" value="{{ old('name', Auth::user()->name ?? '') }}" required>
                    </div>
                    <div class="row">
                        <div class="col-md-7 mb-3">
                            <input type="email" name="email" class="form-control" placeholder="Email *" value="{{ old('email', Auth::user()->email ?? '') }}" required>
                        </div>
                        <div class="col-md-5 mb-3">
                            <input type="text" name="phone" class="form-control" placeholder="Số điện thoại *" value="{{ old('phone', Auth::user()->phone ?? '') }}" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <input type="text" name="address" class="form-control" placeholder="Địa chỉ giao hàng *" value="{{ old('address', Auth::user()->address ?? '') }}" required>
                    </div>

                    <h4 class="mt-4">Phương thức thanh toán</h4>
                    <div class="payment-methods">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="payment_method" id="payment_cod" value="cod" checked>
                            <label class="form-check-label" for="payment_cod">Thanh toán khi nhận hàng (COD)</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="payment_method" id="payment_vnpay" value="vnpay">
                            <label class="form-check-label" for="payment_vnpay">Thanh toán qua VNPAY</label>
                        </div>
                    </div>
                </div>

                {{-- CỘT PHẢI: TÓM TẮT ĐƠN HÀNG --}}
                <div class="col-lg-5">
                    <div class="order-summary">
                        <h4>Đơn hàng của bạn ({{ $cart->total_quantity }} sản phẩm)</h4>
                        
                        @foreach($cart->items as $item)
                            <div class="order-item">
                                <div class="item-details">
                                    <div class="item-image">
                                        <img src="{{ $item->variant->product->thumbnail_url }}" alt="{{ $item->variant->product->name }}">
                                        <span class="item-quantity">{{ $item->quantity }}</span>
                                    </div>
                                    <div class="item-info">
                                        <div class="product-name">{{ $item->variant->product->name }}</div>
                                        <div class="variant-name">{{ $item->variant->name }}</div>
                                    </div>
                                </div>
                                <div class="item-price">
                                    {{ number_format($item->quantity * $item->price_at_order, 0, ',', '.') }}đ
                                </div>
                            </div>
                        @endforeach
                        <hr>
                        <div class="summary-row">
                            <span>Tạm tính:</span>
                            <span>{{ number_format($cart->total_price, 0, ',', '.') }}đ</span>
                        </div>
                        <div class="summary-row">
                            <span>Phí vận chuyển:</span>
                            <span>Miễn phí</span>
                        </div>
                        <hr>
                        <div class="summary-row summary-total">
                            <span>Tổng cộng:</span>
                            <span>{{ number_format($cart->total_price, 0, ',', '.') }}đ</span>
                        </div>

                        <div class="d-grid mt-4">
                             <button type="submit" class="btn btn-place-order">ĐẶT HÀNG</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection