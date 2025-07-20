@extends('layouts.client')

@section('content')
    <div class="container mt-5">
        <h2>Thanh toán</h2>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif


        <h3>Thông tin đơn hàng</h3>

        <table class="table">
            <thead>
                <tr>
                    <th>Sản phẩm</th>
                    <th>Size</th>
                    <th>Số lượng</th>
                    <th>Giá</th>
                    <th>Tổng</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($cart->items as $item)
                    <tr>
                        <td>{{ $item->variant->product->name ?? 'Không tồn tại' }}</td>

                        <td>{{ $item->size }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ number_format($item->variant->price ?? 0) }} VND</td>
                        <td>{{ number_format($item->quantity * ($item->variant->price ?? 0)) }} VND</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- add mã giảm giá --}}
        <h4 class="mt-4">Nhập mã giảm giá</h4>
        <form action="{{ route('cart.apply-discount') }}" method="POST" class="d-flex">
            @csrf
            <input type="text" name="code" class="form-control me-2" placeholder="Nhập mã giảm giá" required>
            <button type="submit" class="btn btn-success">Áp dụng</button>
        </form>
        @if (session('error'))
            <div class="alert alert-danger mt-2">{{ session('error') }}</div>
        @endif

        @if (session('success'))
            <div class="alert alert-success mt-2">{{ session('success') }}</div>
        @endif

        @if (session('discount'))
            <h5 class="mt-3 text-success">Đã áp dụng mã giảm giá: Giảm {{ session('discount') }}%</h5>
        @endif


    
<form action="{{ route('order.store') }}" method="POST">
    @csrf

    {{-- THÔNG TIN KHÁCH HÀNG --}}
    <h3 class="mt-4">Thông tin khách hàng</h3>
    <div class="row">
        <!-- Tên khách hàng -->
        <div class="col-md-6 mb-3">
            <label for="name" class="form-label">Họ và tên</label>
            <input type="text" name="name" id="name" class="form-control"
                   value="{{ old('name', optional($user)->name) }}" required>
        </div>

        <!-- Số điện thoại -->
        <div class="col-md-6 mb-3">
            <label for="phone" class="form-label">Số điện thoại</label>
            <input type="text" name="phone" id="phone" class="form-control"
                   value="{{ old('phone', optional($user)->phone) }}" required>
        </div>

        <!-- Email -->
        <div class="col-md-6 mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" name="email" id="email" class="form-control"
                   value="{{ old('email', optional($user)->email) }}" required>
        </div>

        <!-- Địa chỉ -->
        <div class="col-md-6 mb-3">
            <label for="shipping_address" class="form-label">Địa chỉ</label>
            <input type="text" name="shipping_address" id="shipping_address" class="form-control"
                   value="{{ old('shipping_address', optional($user)->address) }}" required>
        </div>
    </div>

    {{-- PHƯƠNG THỨC THANH TOÁN --}}
    <h4 class="mt-4">Phương thức thanh toán</h4>
    <div class="form-check mb-2">
        <input class="form-check-input" type="radio" name="payment_method" value="cod" id="cod" required checked>
        <label class="form-check-label" for="cod">
            Thanh toán khi nhận hàng (COD)
        </label>
    </div>
    <div class="form-check mb-3">
        <input class="form-check-input" type="radio" name="payment_method" value="vnpay" id="vnpay" required>
        <label class="form-check-label" for="vnpay">
            Thanh toán qua VNPay
        </label>
    </div>

    {{-- DỮ LIỆU SẢN PHẨM (ẨN) --}}
    @foreach ($cart->items as $item)
        <input type="hidden" name="cart_items[{{ $loop->index }}][product_id]" value="{{ $item->variant->product->id }}">
        <input type="hidden" name="cart_items[{{ $loop->index }}][product_variant_id]" value="{{ $item->variant->id }}">
        <input type="hidden" name="cart_items[{{ $loop->index }}][quantity]" value="{{ $item->quantity }}">
        <input type="hidden" name="cart_items[{{ $loop->index }}][price_at_order]" value="{{ $item->variant->price }}">
        <input type="hidden" name="cart_items[{{ $loop->index }}][size]" value="{{ $item->size }}">
    @endforeach

    {{-- DỮ LIỆU ẨN BỔ SUNG --}}
    <input type="hidden" name="user_id" value="{{ Auth::id() }}">
    <input type="hidden" name="payment_status" value="paid">
    <input type="hidden" name="status" value="pending">
    <input type="hidden" name="total_price" value="{{ $total_price }}">

    {{-- TỔNG GIÁ & NÚT SUBMIT --}}
    <h4 class="mt-4">Tổng cộng: {{ number_format($total_price) }} VND</h4>
    <button type="submit" class="btn btn-primary w-100 mt-3">
        Đặt hàng
    </button>
</form>

        {{-- @if (optional($order)->payment_status == 'paid')
                <p>Đơn hàng của bạn đã được thanh toán và đang được xử lý.</p>
            @else
                <button type="submit" class="btn btn-primary mt-3">Đặt hàng</button>
            @endif --}}


    </div>
@endsection
