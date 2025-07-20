@extends('layouts.client')

@section('content')
<div class="container mt-5">
    <h2>Chi tiết đơn hàng #{{ $order->id }}</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <h3>Thông tin giỏ hàng</h3>
    @php
        $cartItems = json_decode($order->cart, true);
    @endphp
    @if(!empty($cartItems))
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Hình ảnh</th>
                    <th>Tên sản phẩm</th>
                    <th>Số lượng</th>
                    <th>Size</th>
                    <th>Giá</th>
                </tr>
            </thead>
            <tbody>
                @foreach($cartItems as $item)
                    <tr>
                        <td>
                            @if (isset($item['image']))
                                <img src="{{ Storage::url($item['image']) }}" alt="Hình ảnh sản phẩm" width="100px">
                            @else
                                <img src="{{ asset('images/default-product.jpg') }}" alt="Hình ảnh mặc định" width="100px">
                            @endif
                        </td>
                        <td>{{ $item['name'] }}</td>
                        <td>{{ $item['quantity'] }}</td>
                        <td>{{ $item['size'] }}</td>
                        <td>{{ number_format($item['price'], 0, ',', '.') }} VND</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p>Không có sản phẩm trong đơn hàng.</p>
    @endif

    <h3 class="mt-4">Thông tin khách hàng</h3>
    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">Họ và tên</label>
            <p>{{ $order->user->name }}</p>
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Số điện thoại</label>
            <p>{{ $order->user->phone }}</p>
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Email</label>
            <p>{{ $order->user->email }}</p>
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Địa chỉ</label>
            <p>{{ $order->user->address }}</p>
        </div>
    </div>

    <h4 class="mt-4">Phương thức thanh toán</h4>
    <p>{{ ucfirst($order->payment_method) }}</p>

    <h4 class="mt-4">Trạng thái thanh toán</h4>
    <p>{{ $order->payment_status }}</p>

    <h4 class="mt-4">Trạng thái đơn hàng</h4>
    <p>{{ $order->status }}</p>

    <a href="{{ route('client.orders.index') }}" class="btn btn-primary mt-3">Quay lại danh sách đơn hàng</a>
</div>
@endsection
