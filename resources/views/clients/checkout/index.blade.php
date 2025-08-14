@extends('layouts.client')

@section('title', 'Thanh toán đơn hàng')

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
                        <hr>
                        <div class="summary-row summary-total"><span>Tổng cộng:</span><span id="final-total-amount">{{ number_format($cart->total_price, 0, ',', '.') }}đ</span></div>
                        <div class="d-grid mt-4"><button type="submit" class="btn btn-place-order">ĐẶT HÀNG</button></div>
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
        .payment-option { position: relative; border: 2px solid #e9ecef; border-radius: 12px; transition: all 0.3s ease; background: #fff; height: 100%;}
        .payment-option:hover { border-color: #0d6efd; }
        .payment-option input[type="radio"] { position: absolute; opacity: 0; width: 100%; height: 100%; cursor: pointer; }
        .payment-option input[type="radio"]:checked + .payment-label { border-color: #0d6efd; background: #e7f1ff; }
        .payment-label { display: flex; align-items: center; padding: 20px; border-radius: 10px; cursor: pointer; margin: 0; height: 100%; }
        .payment-content { display: flex; align-items: center; gap: 15px; }
        .payment-icon { width: 40px; height: 40px; flex-shrink: 0; border-radius: 50%; background: #f0f2f5; display: flex; align-items: center; justify-content: center; font-size: 20px; color: #6c757d; }
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
{{-- JavaScript được gom lại một nơi và đã sửa lỗi --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    // --- Lấy tất cả các element cần thiết ---
    const provinceSelect = document.getElementById('province');
    const districtSelect = document.getElementById('district');
    const wardSelect = document.getElementById('ward');
    const shippingFeeAmountEl = document.getElementById('shipping-fee-amount');
    const finalTotalAmountEl = document.getElementById('final-total-amount');

    if (!provinceSelect || !districtSelect || !wardSelect || !shippingFeeAmountEl || !finalTotalAmountEl) {
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
        finalTotalAmountEl.textContent = formatCurrency(finalTotal);
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
@endpush