@extends('layouts.client')


{{-- @section('content')
    <div class="container">
        <h2>Đơn hàng của bạn</h2>

        @if ($checkouts->isEmpty())
            <p>Không có đơn hàng nào.</p>
        @else
            <table class="table table-bcheckouted">
                <thead>
                    <tr>
                        <th>Mã đơn hàng</th>
                        <th>Tổng giá trị</th>
                        <th>Trạng thái</th>
                        <th>Trạng thái thanh toán</th>
                        <th>Ngày tạo</th>
                        <th>Chi tiết</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($checkouts as $checkout)
                        <tr>
                            <td>{{ $checkout->id }}</td>
                            <td>{{ number_format($checkout->total_price, 0, ',', '.') }} VND</td>
                            <td>{{ $checkout->status }}</td>
                            <td>{{ $checkout->payment_status }}</td>
                            <td>{{ $checkout->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <a href="{{ route('client.checkouts.show', $checkout->id) }}" class="btn btn-info">Xem chi
                                    tiết</a>

                                <!-- Hiển thị nút hủy nếu trạng thái là "Đang chờ xử lý" -->
                                @if ($checkout->status === 'pending')
                                    <a href="{{ route('client.checkouts.cancel', $checkout->id) }}" class="btn btn-danger"
                                        onclick="return confirm('Bạn có chắc chắn muốn hủy đơn hàng này?')">
                                        Hủy đơn hàng
                                    </a>
                                @elseif ($checkout->status === 'cancelled')
                                    <a href="{{ route('client.checkouts.restore', $checkout->id) }}" class="btn btn-warning"
                                        onclick="return confirm('Bạn có muốn khôi phục đơn hàng đã hủy này không?')">
                                        Khôi phục đơn hàng
                                    </a>
                                @endif

                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
@endsection --}}
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
    .discount-section {
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 15px;
        background-color: #f9f9f9;
    }
    .discount-message {
        font-size: 14px;
        margin-top: 8px;
    }
    .discount-message.success {
        color: #28a745;
    }
    .discount-message.error {
        color: #dc3545;
    }
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    let originalTotal = {{ $cart->total_price }};
    let appliedDiscount = 0;
    
    $('#apply-discount-btn').click(function() {
        const discountCode = $('#discount-code-input').val().trim();
        
        if (!discountCode) {
            showDiscountMessage('Vui lòng nhập mã giảm giá', 'error');
            return;
        }
        
        // Disable button while processing
        $(this).prop('disabled', true).text('Đang xử lý...');
        
        // Gửi request kiểm tra mã giảm giá
        $.ajax({
            url: '{{ route("client.checkout.validateDiscount") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                discount_code: discountCode,
                total_amount: originalTotal
            },
            success: function(response) {
                if (response.success) {
                    appliedDiscount = response.discount_amount;
                    updateTotal();
                    showDiscountMessage(response.message, 'success');
                    $('#discount-row').show();
                    $('#discount-amount').text('-' + formatCurrency(appliedDiscount));
                } else {
                    showDiscountMessage(response.message, 'error');
                    resetDiscount();
                }
            },
            error: function() {
                showDiscountMessage('Có lỗi xảy ra, vui lòng thử lại', 'error');
                resetDiscount();
            },
            complete: function() {
                $('#apply-discount-btn').prop('disabled', false).text('Áp dụng');
            }
        });
    });
    
    function updateTotal() {
        const finalTotal = originalTotal - appliedDiscount;
        $('#final-total').text(formatCurrency(finalTotal));
    }
    
    function resetDiscount() {
        appliedDiscount = 0;
        updateTotal();
        $('#discount-row').hide();
        $('#discount-amount').text('-0đ');
    }
    
    function showDiscountMessage(message, type) {
        $('#discount-message').html('<div class="discount-message ' + type + '">' + message + '</div>');
    }
    
    function formatCurrency(amount) {
        return new Intl.NumberFormat('vi-VN').format(amount) + 'đ';
    }
    
    // Reset discount when code input changes
    $('#discount-code-input').on('input', function() {
        if (appliedDiscount > 0) {
            resetDiscount();
            showDiscountMessage('', '');
        }
    });
});
</script>
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
                        
                        {{-- Mã giảm giá --}}
                        <div class="discount-section mt-3">
                            <div class="input-group">
                                <input type="text" 
                                       name="discount_code" 
                                       class="form-control" 
                                       placeholder="Nhập mã giảm giá"
                                       value="{{ old('discount_code') }}"
                                       id="discount-code-input">
                                <button type="button" 
                                        class="btn btn-outline-primary" 
                                        id="apply-discount-btn">
                                    Áp dụng
                                </button>
                            </div>
                            <div id="discount-message" class="mt-2"></div>
                        </div>
                        
                        <hr>
                        <div class="summary-row" id="discount-row" style="display: none;">
                            <span>Giảm giá:</span>
                            <span id="discount-amount" class="text-success">-0đ</span>
                        </div>
                        <div class="summary-row summary-total">
                            <span>Tổng cộng:</span>
                            <span id="final-total">{{ number_format($cart->total_price, 0, ',', '.') }}đ</span>
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
