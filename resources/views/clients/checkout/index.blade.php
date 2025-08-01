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
            <!-- Hidden fields để lưu thông tin mã giảm giá -->
            <input type="hidden" name="discount_code" id="discount-code-hidden" value="">
            <input type="hidden" name="discount_value" id="discount-value-hidden" value="0">
            <input type="hidden" name="final_total" id="final-total-hidden" value="{{ $cart->total_price }}">
            <div class="row">
                {{-- CỘT TRÁI: THÔNG TIN GIAO HÀNG --}}
                <div class="col-lg-7 checkout-form">
                    <h4>Thông tin giao hàng</h4>
                    @guest
                        <p>Bạn đã có tài khoản? <a href="{{ route('login') }}">Đăng nhập</a> để điền thông tin nhanh hơn.</p>
                    @endguest

                    {{-- Form nhập mã giảm giá --}}
                    <div class="checkout-section">
                        <h5 class="section-title">
                            Mã giảm giá
                        </h5>
                        <div class="discount-container">
                            <div class="input-group discount-input-group">
                                <input type="text" id="discount-code" class="form-control discount-input" placeholder="Nhập mã giảm giá của bạn">
                                <button type="button" id="apply-discount-btn" class="btn btn-apply-discount">
                                    <i class="fas fa-check me-1"></i>Áp dụng
                                </button>
                            </div>
                            <div id="discount-message" class="mt-2"></div>
                            <div id="applied-discount" class="mt-3" style="display: none;">
                                <div class="applied-discount-card">
                                    <div class="discount-info">
                                        <div class="discount-badge">
                                            <i class="fas fa-check-circle"></i>
                                            <span id="applied-code"></span>
                                        </div>
                                        <div class="discount-details">
                                            <span id="discount-value"></span>
                                        </div>
                                    </div>
                                    <button type="button" id="remove-discount-btn" class="btn-remove-discount">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Thông tin giao hàng --}}
                    <div class="checkout-section">
                        <h5 class="section-title">
                            Thông tin giao hàng
                        </h5>
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Họ và tên *</label>
                                <input type="text" name="name" class="form-control modern-input" value="{{ old('name', Auth::user() ? Auth::user()->name : '') }}" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-7">
                                <label class="form-label">Email *</label>
                                <input type="email" name="email" class="form-control modern-input" value="{{ old('email', Auth::user() ? Auth::user()->email : '') }}" required>
                            </div>
                            <div class="form-group col-md-5">
                                <label class="form-label">Số điện thoại *</label>
                                <input type="text" name="phone" class="form-control modern-input" value="{{ old('phone', Auth::user() ? Auth::user()->phone : '') }}" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Địa chỉ giao hàng *</label>
                                <input type="text" name="address" class="form-control modern-input" value="{{ old('address', Auth::user() ? Auth::user()->address : '') }}" required>
                            </div>
                        </div>
                    </div>

                    <h4 class="mt-4 mb-3">
                        Phương thức thanh toán
                    </h4>
                    <div class="payment-methods">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="payment-option">
                                    <input class="form-check-input" type="radio" name="payment_method" id="payment_cod" value="cod" checked>
                                    <label class="form-check-label payment-label" for="payment_cod">
                                        <div class="payment-content">
                                            <div class="payment-icon">
                                                <i class="fas fa-money-bill-wave"></i>
                                            </div>
                                            <div class="payment-info">
                                                <div class="payment-title">Thanh toán khi nhận hàng</div>
                                                <div class="payment-subtitle">COD - Cash on Delivery</div>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="payment-option">
                                    <input class="form-check-input" type="radio" name="payment_method" id="payment_vnpay" value="vnpay">
                                    <label class="form-check-label payment-label" for="payment_vnpay">
                                        <div class="payment-content">
                                            <div class="payment-icon">
                                                <i class="fas fa-qrcode"></i>
                                            </div>
                                            <div class="payment-info">
                                                <div class="payment-title">Thanh toán qua VNPAY</div>
                                                <div class="payment-subtitle">QR Code / Thẻ ngân hàng</div>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- CỘT PHẢI: TÓM TẮT ĐƠN HÀNG --}}
                <div class="col-lg-5">
                    <div class="order-summary">
                        <h4>Đơn hàng của bạn ({{ $cart->total_quantity }} sản phẩm)</h4>
                        
                        @foreach($cart->items as $item)
                            @if($item->variant && $item->variant->product)
                                <div class="order-item">
                                    <div class="item-details">
                                        <div class="item-image">
                                            <img src="{{ $item->variant->product->thumbnail_url ?? '' }}" alt="{{ $item->variant->product->name ?? 'Sản phẩm' }}">
                                            <span class="item-quantity">{{ $item->quantity }}</span>
                                        </div>
                                        <div class="item-info">
                                            <div class="product-name">{{ $item->variant->product->name ?? 'Sản phẩm không tồn tại' }}</div>
                                            <div class="variant-name">{{ $item->variant->name ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                    <div class="item-price">
                                        {{ number_format($item->quantity * $item->price_at_order, 0, ',', '.') }}đ
                                    </div>
                                </div>
                            @endif
                        @endforeach
                        <hr>
                        <div class="summary-row">
                            <span>Tạm tính:</span>
                            <span id="subtotal">{{ number_format($cart->total_price, 0, ',', '.') }}đ</span>
                        </div>
                        <div class="summary-row" id="discount-row" style="display: none;">
                            <span>Giảm giá:</span>
                            <span id="discount-amount" class="text-success">-0đ</span>
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const discountCodeInput = document.getElementById('discount-code');
    const applyDiscountBtn = document.getElementById('apply-discount-btn');
    const discountMessage = document.getElementById('discount-message');
    const appliedDiscount = document.getElementById('applied-discount');
    const appliedCode = document.getElementById('applied-code');
    const discountValue = document.getElementById('discount-value');
    const removeDiscountBtn = document.getElementById('remove-discount-btn');
    const discountRow = document.getElementById('discount-row');
    const discountAmount = document.getElementById('discount-amount');
    const totalAmount = document.getElementById('total-amount');
    const subtotal = document.getElementById('subtotal');
    
    let currentDiscount = null;
    let originalTotal = {{ $cart->total_price }};
    
    // Format số tiền
    function formatCurrency(amount) {
        return new Intl.NumberFormat('vi-VN').format(amount) + 'đ';
    }
    
    // Cập nhật tổng tiền
    function updateTotal() {
        let finalTotal = originalTotal;
        if (currentDiscount) {
            finalTotal = Math.max(0, originalTotal - currentDiscount.discountValue);
        }
        totalAmount.textContent = formatCurrency(finalTotal);
        
        // Cập nhật hidden fields
        document.getElementById('discount-code-hidden').value = currentDiscount ? currentDiscount.code : '';
        document.getElementById('discount-value-hidden').value = currentDiscount ? currentDiscount.discountValue : 0;
        document.getElementById('final-total-hidden').value = finalTotal;
    }
    
    // Xử lý áp dụng mã giảm giá
    applyDiscountBtn.addEventListener('click', function() {
        const code = discountCodeInput.value.trim();
        if (!code) {
            discountMessage.innerHTML = '<div class="alert alert-warning">Vui lòng nhập mã giảm giá!</div>';
            return;
        }
        
        // Hiển thị loading
        applyDiscountBtn.disabled = true;
        applyDiscountBtn.textContent = 'Đang xử lý...';
        
        fetch('{{ route("cart.apply-discount") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                code: code,
                order_amount: originalTotal
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                currentDiscount = {
                    code: data.discount.code,
                    discountType: data.discount.discount_type,
                    discountValue: data.discount.discount_type === 'amount' ? 
                        parseFloat(data.discount.amount) : 
                        (originalTotal * parseFloat(data.discount.discount) / 100)
                };
                
                // Hiển thị thông tin mã đã áp dụng
                appliedCode.textContent = currentDiscount.code;
                discountValue.textContent = currentDiscount.discountType === 'amount' ? 
                    `Giảm ${formatCurrency(currentDiscount.discountValue)}` : 
                    `Giảm ${data.discount.discount}%`;
                
                appliedDiscount.style.display = 'block';
                discountRow.style.display = 'flex';
                discountAmount.textContent = `-${formatCurrency(currentDiscount.discountValue)}`;
                
                updateTotal();
                discountMessage.innerHTML = '<div class="alert alert-success">Áp dụng mã giảm giá thành công!</div>';
                discountCodeInput.value = '';
            } else {
                discountMessage.innerHTML = `<div class="alert alert-danger">${data.error}</div>`;
            }
        })
        .catch(error => {
            discountMessage.innerHTML = '<div class="alert alert-danger">Có lỗi xảy ra, vui lòng thử lại!</div>';
        })
        .finally(() => {
            applyDiscountBtn.disabled = false;
            applyDiscountBtn.textContent = 'Áp dụng';
        });
    });
    
    // Xử lý xóa mã giảm giá
    removeDiscountBtn.addEventListener('click', function() {
        currentDiscount = null;
        appliedDiscount.style.display = 'none';
        discountRow.style.display = 'none';
        updateTotal();
        discountMessage.innerHTML = '';
    });
    
    // Xử lý nhấn Enter trong input
    discountCodeInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            applyDiscountBtn.click();
        }
    });
});
</script>

<style>
/* Style chung cho checkout */
.checkout-section {
    background: white;
    border-radius: 15px;
    padding: 25px;
    margin-bottom: 25px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
    border: 1px solid #e9ecef;
}

.section-title {
    color: #2c3e50;
    font-weight: 600;
    font-size: 18px;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 2px solid #f8f9fa;
}

/* Style cho form inputs */
.modern-input {
    border: 2px solid #e9ecef;
    border-radius: 10px;
    padding: 12px 15px;
    font-size: 16px;
    transition: all 0.3s ease;
    background: #f8f9fa;
}

.modern-input:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    background: white;
}

.form-label {
    font-weight: 600;
    color: #495057;
    margin-bottom: 8px;
    font-size: 14px;
}

.form-row {
    display: flex;
    gap: 20px;
    margin-bottom: 20px;
}

.form-group {
    flex: 1;
}

.form-group.col-md-7 {
    flex: 0 0 58.333333%;
}

.form-group.col-md-5 {
    flex: 0 0 41.666667%;
}

/* Style cho mã giảm giá */
.discount-container {
    background: #f8f9fa;
    border-radius: 12px;
    padding: 20px;
}

.discount-input-group {
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.discount-input {
    border: none;
    background: white;
    padding: 15px 20px;
    font-size: 16px;
}

.discount-input:focus {
    box-shadow: none;
    background: white;
}

.btn-apply-discount {
    background: linear-gradient(135deg, #ff6b35, #f7931e);
    border: none;
    color: white;
    font-weight: 600;
    padding: 15px 25px;
    border-radius: 0 10px 10px 0;
    transition: all 0.3s ease;
}

.btn-apply-discount:hover {
    background: linear-gradient(135deg, #e55a2b, #e0851a);
    transform: translateY(-1px);
    color: white;
}

.applied-discount-card {
    background: linear-gradient(135deg, #28a745, #20c997);
    color: white;
    border-radius: 12px;
    padding: 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
}

.discount-info {
    display: flex;
    flex-direction: column;
    gap: 5px;
}

.discount-badge {
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: 600;
    font-size: 16px;
}

.discount-details {
    font-size: 14px;
    opacity: 0.9;
}

.btn-remove-discount {
    background: rgba(255, 255, 255, 0.2);
    border: none;
    color: white;
    width: 35px;
    height: 35px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
}

.btn-remove-discount:hover {
    background: rgba(255, 255, 255, 0.3);
    transform: scale(1.1);
}

/* Style cho phần thanh toán */
.payment-option {
    position: relative;
    border: 2px solid #e9ecef;
    border-radius: 12px;
    padding: 0;
    transition: all 0.3s ease;
    background: #fff;
}

.payment-option:hover {
    border-color: #007bff;
    box-shadow: 0 4px 12px rgba(0, 123, 255, 0.15);
}

.payment-option input[type="radio"] {
    position: absolute;
    opacity: 0;
    width: 100%;
    height: 100%;
    cursor: pointer;
}

.payment-option input[type="radio"]:checked + .payment-label {
    background: linear-gradient(135deg, #007bff, #0056b3);
    color: white;
}

.payment-option input[type="radio"]:checked + .payment-label .payment-icon {
    background: rgba(255, 255, 255, 0.2);
    color: white;
}

.payment-label {
    display: block;
    padding: 20px;
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.3s ease;
    margin: 0;
    height: 100%;
}

.payment-content {
    display: flex;
    align-items: center;
    gap: 15px;
}

.payment-icon {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: #f8f9fa;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    color: #6c757d;
    transition: all 0.3s ease;
}

.payment-info {
    flex: 1;
}

.payment-title {
    font-weight: 600;
    font-size: 16px;
    margin-bottom: 4px;
}

.payment-subtitle {
    font-size: 14px;
    opacity: 0.8;
}

/* Style cho nút đặt hàng */
.btn-place-order {
    background: linear-gradient(135deg, #28a745, #20c997);
    border: none;
    color: white;
    font-weight: 600;
    font-size: 18px;
    padding: 15px 30px;
    border-radius: 10px;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
}

.btn-place-order:hover {
    background: linear-gradient(135deg, #218838, #1ea085);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(40, 167, 69, 0.4);
    color: white;
}

/* Style cho phần tóm tắt đơn hàng */
.order-summary {
    background: #f8f9fa;
    border-radius: 15px;
    padding: 25px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.order-summary h4 {
    color: #2c3e50;
    font-weight: 700;
    margin-bottom: 20px;
    border-bottom: 2px solid #e9ecef;
    padding-bottom: 10px;
}

.order-item {
    background: white;
    border-radius: 10px;
    padding: 15px;
    margin-bottom: 10px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

.summary-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px 0;
    font-size: 16px;
}

.summary-total {
    font-weight: 700;
    font-size: 18px;
    color: #2c3e50;
    border-top: 2px solid #e9ecef;
    padding-top: 15px;
    margin-top: 10px;
}

/* Responsive */
@media (max-width: 768px) {
    .form-row {
        flex-direction: column;
        gap: 15px;
    }
    
    .form-group.col-md-7,
    .form-group.col-md-5 {
        flex: 1;
    }
    
    .checkout-section {
        padding: 20px;
        margin-bottom: 20px;
    }
    
    .section-title {
        font-size: 16px;
    }
    
    .payment-content {
        flex-direction: column;
        text-align: center;
        gap: 10px;
    }
    
    .payment-icon {
        width: 40px;
        height: 40px;
        font-size: 16px;
    }
    
    .payment-title {
        font-size: 14px;
    }
    
    .payment-subtitle {
        font-size: 12px;
    }
    
    .discount-input-group {
        flex-direction: column;
    }
    
    .btn-apply-discount {
        border-radius: 0 0 10px 10px;
        margin-top: -1px;
    }
    
    .applied-discount-card {
        flex-direction: column;
        gap: 15px;
        text-align: center;
    }
}
</style>
@endsection
