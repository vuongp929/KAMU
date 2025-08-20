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
        position: relative;
        overflow: hidden;
        
        box-sizing: border-box;
    }
    
    /* Đảm bảo voucher section không ảnh hưởng layout */
    .voucher-section {
        margin: 15px 0 !important;
        width: 100% !important;
        box-sizing: border-box !important;
        clear: both !important;
    }
    
    /* Đảm bảo order items container ổn định */
    .order-items-container {
        width: 100% !important;
        overflow: hidden !important;
        margin-bottom: 15px !important;
    }
    .order-item {
        display: flex !important;
        justify-content: space-between !important;
        align-items: flex-start !important;
        margin-bottom: 15px !important;
        padding: 15px !important;
        background: white !important;
        border-radius: 10px !important;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05) !important;
        border-bottom: 1px solid #f5f5f5 !important;
        width: 100% !important;
        box-sizing: border-box !important;
        position: relative !important;
        overflow: hidden !important;
        gap: 15px !important;
    }
    .order-item:last-child {
        border-bottom: none;
        margin-bottom: 0;
    }
    .order-item .item-details {
        display: flex !important;
        align-items: flex-start !important;
        flex: 1 !important;
        min-width: 0 !important;
        overflow: hidden !important;
        gap: 15px !important;
    }
    .order-item .item-image {
        position: relative !important;
        margin-right: 15px !important;
        flex-shrink: 0 !important;
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
    .item-info {
        flex: 1 !important;
        min-width: 0 !important;
        overflow: hidden !important;
    }
    .item-info .product-name {
        font-weight: 600 !important;
        color: #333 !important;
        margin-bottom: 4px !important;
        word-wrap: break-word !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
        display: -webkit-box !important;
        -webkit-line-clamp: 2 !important;
        -webkit-box-orient: vertical !important;
        white-space: normal !important;
        line-height: 1.4 !important;
        max-height: 2.8em !important;
    }
    .item-info .variant-name {
        color: #777 !important;
        font-size: 14px !important;
        word-wrap: break-word !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
        white-space: nowrap !important;
        max-width: 100% !important;
    }
    .item-price {
        font-weight: 600 !important;
        color: #555 !important;
        flex-shrink: 0 !important;
        text-align: right !important;
        white-space: nowrap !important;
        font-size: 14px !important;
        min-width: 80px !important;
    }
    
    /* Responsive cho mobile */
    @media (max-width: 768px) {
        .order-item {
            flex-direction: column !important;
            align-items: flex-start !important;
        }
        .order-item .item-details {
            width: 100% !important;
            margin-bottom: 10px !important;
        }
        .item-info {
            max-width: calc(100% - 80px) !important;
        }
        .item-price {
            width: auto !important;
            min-width: auto !important;
            text-align: left !important;
            margin-left: 0 !important;
            font-size: 16px !important;
        }
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

        <form id="checkout-form" action="{{ route('client.checkout.placeOrder') }}" method="POST">
            @csrf
            <div class="row">
                {{-- CỘT TRÁI: THÔNG TIN GIAO HÀNG --}}
                <div class="col-lg-7 checkout-form">
                    {{-- Thông tin người nhận --}}
                    <div class="checkout-section">
                        <h5 class="section-title">Thông tin người nhận</h5>
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label for="name" class="form-label">Họ và tên *</label>
                                <input type="text" id="name" name="name" class="form-control" value="{{ old('name', Auth::user()->name ?? '') }}" required>
                            </div>
                            <div class="col-md-7 mb-3">
                                <label for="email" class="form-label">Email *</label>
                                <input type="email" id="email" name="email" class="form-control" value="{{ old('email', Auth::user()->email ?? '') }}" required>
                            </div>
                            <div class="col-md-5 mb-3">
                                <label for="phone" class="form-label">Số điện thoại *</label>
                                <input type="text" id="phone" name="phone" class="form-control" value="{{ old('phone', Auth::user()->phone ?? '') }}" required>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Địa chỉ nhận hàng --}}
                    <div class="checkout-section">
                        <h5 class="section-title">Địa chỉ nhận hàng</h5>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="province" class="form-label">Tỉnh/Thành phố *</label>
                                <select name="province_id" id="province" class="form-select" required></select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="district" class="form-label">Quận/Huyện *</label>
                                <select name="district_id" id="district" class="form-select" required></select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="ward" class="form-label">Phường/Xã *</label>
                                <select name="ward_code" id="ward" class="form-select" required></select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="address" class="form-label">Địa chỉ cụ thể (số nhà, tên đường) *</label>
                            <input type="text" id="address" name="address" class="form-control" value="{{ old('address') }}" required>
                        </div>
                    </div>
                    
                    {{-- Phương thức thanh toán --}}
                    <h4 class="mt-4 mb-3">Phương thức thanh toán</h4>
                    <div class="row">
                        <div class="col-md-4 mb-3"><div class="payment-option"><input class="form-check-input" type="radio" name="payment_method" id="payment_cod" value="cod" checked><label class="form-check-label payment-label" for="payment_cod"><div class="payment-content"><div class="payment-icon"><i class="fas fa-money-bill-wave"></i></div><div class="payment-info"><div class="payment-title">Thanh toán khi nhận hàng</div></div></div></label></div></div>
                        <div class="col-md-4 mb-3"><div class="payment-option"><input class="form-check-input" type="radio" name="payment_method" id="payment_vnpay" value="vnpay"><label class="form-check-label payment-label" for="payment_vnpay"><div class="payment-content"><div class="payment-icon"><i class="fas fa-qrcode"></i></div><div class="payment-info"><div class="payment-title">Thanh toán VNPAY</div></div></div></label></div></div>
                        <div class="col-md-4 mb-3"><div class="payment-option"><input class="form-check-input" type="radio" name="payment_method" id="payment_momo" value="momo"><label class="form-check-label payment-label" for="payment_momo"><div class="payment-content"><div class="payment-icon"><img src="https://developers.momo.vn/v3/vi/assets/images/logo.svg" alt="Momo" style="height: 24px;"></div><div class="payment-info"><div class="payment-title">Thanh toán Ví MoMo</div></div></div></label></div></div>
                    </div>
                </div>

                {{-- CỘT PHẢI: TÓM TẮT ĐƠN HÀNG --}}
                <div class="col-lg-5">
                    <div class="order-summary">
                        <h4>Đơn hàng của bạn ({{ $cart->items->count() }} sản phẩm)</h4>
                        
                        @foreach($cart->items as $item)
                            <div class="order-item"><div class="item-details"><div class="item-image"><img src="{{ $item->variant->product->thumbnail_url ?? '' }}"><span class="item-quantity">{{ $item->quantity }}</span></div><div class="item-info"><div class="product-name">{{ $item->variant->product->name ?? 'Sản phẩm lỗi' }}</div><div class="variant-name">{{ $item->variant->name ?? 'N/A' }}</div></div></div><div class="item-price">{{ number_format($item->quantity * $item->variant->price, 0, ',', '.') }}đ</div></div>
                        @endforeach
                        
                        <hr>
                        <div class="summary-row"><span>Tạm tính:</span><span id="subtotal-amount">{{ number_format($cart->total_price, 0, ',', '.') }}đ</span></div>
                        <div class="summary-row" id="shipping-fee-row"><span>Phí vận chuyển:</span><span id="shipping-fee-amount">Vui lòng chọn địa chỉ</span></div>
                        <div class="summary-row" id="discount-row" style="display: none;"><span>Giảm giá:</span><span id="discount-amount">-0đ</span></div>
                        <hr>
                        <div class="summary-row summary-total">
                            <span>Tổng cộng:</span>
                            <span id="total-amount">{{ number_format($cart->total_price, 0, ',', '.') }}đ</span>
                        </div>

                        @include('clients.checkout.voucher-component')

                        {{-- Hidden inputs for discount and shipping --}}
                        <input type="hidden" id="discount-code-hidden" name="discount_code" value="">
                        <input type="hidden" id="discount-value-hidden" name="discount_value" value="0">
                        <input type="hidden" id="shipping-fee-hidden" name="shipping_fee" value="0">
                        <input type="hidden" id="final-total-hidden" name="final_total" value="{{ $cart->total_price }}" data-order-total="{{ $cart->total_price }}">

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

@push('styles')
    {{-- CSS được gom lại một nơi --}}
    <style>
        .checkout-page { background-color: #f9f9f9; padding: 40px 0; }
        .checkout-form h4, .order-summary h4 { font-weight: 600; color: #333; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 1px solid #eee; }
        .form-control, .form-select { border-radius: 8px; padding: 10px 15px; }
        .checkout-section { background: white; border-radius: 15px; padding: 25px; margin-bottom: 25px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05); }
        .section-title { font-weight: 600; font-size: 18px; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #f8f9fa; }
        .form-label { font-weight: 600; color: #495057; margin-bottom: 8px; font-size: 14px; }
        .payment-option { position: relative; border: 2px solid #e9ecef; border-radius: 12px; transition: all 0.3s ease; background: #fff; min-height: 80px;}
        .payment-option:hover { border-color: #0d6efd; }
        .payment-option input[type="radio"] { position: absolute; opacity: 0; width: 100%; height: 100%; cursor: pointer; }
        .payment-option input[type="radio"]:checked + .payment-label { border-color: #0d6efd; background: #e7f1ff; }
        .payment-label { display: flex; align-items: center; padding: 15px; border-radius: 10px; cursor: pointer; margin: 0; min-height: 80px; }
        .payment-content { display: flex; align-items: center; gap: 15px; }
        .payment-icon { width: 35px; height: 35px; flex-shrink: 0; border-radius: 50%; background: #f0f2f5; display: flex; align-items: center; justify-content: center; font-size: 18px; color: #6c757d; }
        .payment-title { font-weight: 600; }
        .order-summary { background-color: #fff; border-radius: 10px; padding: 25px; border: 1px solid #eee; }
        .order-item { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; padding-bottom: 15px; border-bottom: 1px solid #f5f5f5; }
        .order-item:last-child { border-bottom: none; margin-bottom: 0; padding-bottom: 0; }
        .item-details { display: flex; align-items: center; }
        .item-image { position: relative; margin-right: 15px; }
        .item-image img { width: 65px; height: 65px; object-fit: cover; border-radius: 8px; }
        .item-quantity { position: absolute; top: -8px; right: -8px; background-color: #5d3b80; color: white; font-size: 12px; font-weight: bold; width: 22px; height: 22px; border-radius: 50%; display: flex; align-items: center; justify-content: center; border: 2px solid white; }
        .product-name { font-weight: 600; }
        .variant-name { color: #777; font-size: 14px; }
        .item-price { font-weight: 600; }
        .summary-row { display: flex; justify-content: space-between; margin-bottom: 10px; }
        .summary-total { font-size: 1.2rem; font-weight: bold; }
        .btn-place-order { background-color: #ea73ac; color: white; font-weight: bold; padding: 12px; font-size: 1.1rem; border: none; width: 100%; }
        .btn-place-order:hover { background-color: #d66095; color: white; }
    </style>
@endpush

@push('scripts')
{{-- JavaScript cho shipping và các chức năng khác --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    // --- Lấy tất cả các element cần thiết ---
    const provinceSelect = document.getElementById('province');
    const districtSelect = document.getElementById('district');
    const wardSelect = document.getElementById('ward');
    const shippingFeeAmountEl = document.getElementById('shipping-fee-amount');
    const totalAmountEl = document.getElementById('total-amount');

    if (!provinceSelect || !districtSelect || !wardSelect || !shippingFeeAmountEl || !totalAmountEl) {
        console.error("Lỗi: Không tìm thấy các element HTML cần thiết cho việc tính phí vận chuyển.");
        return;
    }

    // --- Các biến trạng thái ---
    let subtotal = {{ (int)$cart->total_price }};
    let shippingFee = 0;

    // --- Các hàm chức năng ---
    const formatCurrency = (amount) => new Intl.NumberFormat('vi-VN').format(amount) + 'đ';

    const updateTotal = () => {
        const finalTotal = subtotal + shippingFee;
        totalAmountEl.textContent = formatCurrency(finalTotal);
        
        // Cập nhật final_total và shipping_fee hidden inputs
        const finalTotalHidden = document.getElementById('final-total-hidden');
        const shippingFeeHidden = document.getElementById('shipping-fee-hidden');
        if (finalTotalHidden) {
            finalTotalHidden.value = finalTotal;
        }
        if (shippingFeeHidden) {
            shippingFeeHidden.value = shippingFee;
        }
    };

    const fetchProvinces = async () => {
        try {
            const response = await fetch('{{ route("shipping.provinces") }}');
            if (!response.ok) throw new Error('Lỗi mạng khi tải tỉnh/thành');
            const result = await response.json();
            if (result && Array.isArray(result.data)) {
                provinceSelect.innerHTML = '<option value="">-- Chọn Tỉnh/Thành --</option>';
                result.data.forEach(p => provinceSelect.innerHTML += `<option value="${p.ProvinceID}">${p.ProvinceName}</option>`);
            }
        } catch (error) { console.error(error); }
    };

    const fetchDistricts = async (provinceId) => {
        districtSelect.innerHTML = '<option value="">-- Chọn Quận/Huyện --</option>';
        wardSelect.innerHTML = '<option value="">-- Chọn Phường/Xã --</option>';
        if (!provinceId) return;
        try {
            const response = await fetch(`{{ route("shipping.districts") }}?province_id=${provinceId}`);
            const result = await response.json();
            if (result && Array.isArray(result.data)) {
                result.data.forEach(d => districtSelect.innerHTML += `<option value="${d.DistrictID}">${d.DistrictName}</option>`);
            }
        } catch (error) { console.error(error); }
    };

    const fetchWards = async (districtId) => {
        wardSelect.innerHTML = '<option value="">-- Chọn Phường/Xã --</option>';
        if (!districtId) return;
        try {
            const response = await fetch(`{{ route("shipping.wards") }}?district_id=${districtId}`);
            const result = await response.json();
            if (result && Array.isArray(result.data)) {
                result.data.forEach(w => wardSelect.innerHTML += `<option value="${w.WardCode}">${w.WardName}</option>`);
            }
        } catch (error) { console.error(error); }
    };

    const calculateShippingFee = async () => {
        const districtId = districtSelect.value;
        const wardCode = wardSelect.value;
        
        shippingFee = 0;
        updateTotal();

        if (!districtId || !wardCode) {
            shippingFeeAmountEl.textContent = 'Vui lòng chọn địa chỉ';
            return;
        }
        
        shippingFeeAmountEl.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
        try {
            const response = await fetch('{{ route("shipping.calculateFee") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ to_district_id: districtId, to_ward_code: wardCode })
            });
            const result = await response.json();
            if(result && result.data && result.data.total) {
                shippingFee = result.data.total;
                shippingFeeAmountEl.textContent = formatCurrency(shippingFee);
            } else {
                shippingFeeAmountEl.textContent = 'Không thể tính phí';
                console.error("Lỗi GHN:", result.message || 'Lỗi không xác định');
            }
        } catch (error) {
            shippingFeeAmountEl.textContent = 'Lỗi';
            console.error("Lỗi API:", error);
        }
        updateTotal();
    };
    
    // --- Gán sự kiện ---
    provinceSelect.addEventListener('change', () => fetchDistricts(provinceSelect.value));
    districtSelect.addEventListener('change', () => fetchWards(districtSelect.value));
    wardSelect.addEventListener('change', calculateShippingFee);

    fetchProvinces();
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
    padding: 15px;
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.3s ease;
    margin: 0;
    min-height: auto;
}

.payment-content {
    display: flex;
    align-items: center;
    gap: 12px;
}

.payment-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #f8f9fa;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
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

/* .order-item styles moved to top section to avoid conflicts */

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
@media (max-width: 576px) {
    .payment-methods .col-md-6 {
        flex: 0 0 100%;
        max-width: 100%;
        padding: 0 10px;
    }
    
    .payment-content {
        flex-direction: column;
        text-align: center;
        gap: 8px;
    }
}

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
        flex-direction: row;
        text-align: left;
        gap: 10px;
    }
    
    .payment-methods .row {
        margin: 0;
    }
    
    .payment-methods .col-md-6 {
        padding: 0 5px;
        margin-bottom: 15px;
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
    
    /* Fix order item layout on mobile */
    .order-item {
        flex-direction: column !important;
        align-items: flex-start !important;
        gap: 10px !important;
    }
    
    .order-item .item-details {
        width: 100% !important;
    }
    
    .order-item .item-price {
        align-self: flex-end !important;
        min-width: auto !important;
    }
    
    .item-info {
        margin-right: 0 !important;
    }
}
</style>
@endpush

{{-- Include discount modal --}}
@include('clients.checkout.discount-modal')

@push('scripts')
<script src="{{ asset('js/voucher-checkout.js') }}"></script>
<script src="{{ asset('js/checkout-form-persistence.js') }}"></script>
@endpush
