@extends('layouts.admin')

@section('title', 'Chi tiết đơn hàng')

@section('content')
    <div class="container-fluid">
        <h4 class="mb-4">Chi tiết đơn hàng #{{ $order->id }}</h4>

        <div class="mb-4">
            <strong>Khách hàng:</strong> {{ $order->customer->name }} <br>
            <strong>Email:</strong> {{ $order->customer->email }} <br>
            <strong>Trạng thái:</strong> {{ $order->status }} <br>
            <strong>Tổng tiền:</strong> {{ number_format($order->total_price, 0, ',', '.') }} VNĐ <br>
            <strong>Ngày tạo:</strong> {{ $order->created_at->format('d-m-Y H:i') }}
        </div>

        <h5>Sản phẩm trong đơn hàng</h5>
        @php
            $cartItems = json_decode($order->cart, true);
        @endphp
        @if (!empty($cartItems))
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
                    @foreach ($cartItems as $item)
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

        <a href="{{ route('admins.orders.index') }}" class="btn btn-secondary">Quay lại</a>
    </div>
@endsection
