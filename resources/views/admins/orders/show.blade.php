@extends('layouts.admin')

@section('title', 'Chi tiết đơn hàng')

@section('content')
    <div class="container-fluid">
        <h4 class="mb-4 text-primary">📦 Chi tiết đơn hàng #{{ $order->id }}</h4>

        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <p><strong>👤 Khách hàng:</strong> {{ $order->customer?->name ?? 'Không xác định' }}</p>
                        <p><strong>📧 Email:</strong> {{ $order->customer->email ?? 'Không có email' }}</p>
                        <p>
                            <strong>📌 Trạng thái:</strong>
                            <span
                                class="badge 
                        @if ($order->status === 'pending') bg-warning text-dark
                        @elseif($order->status === 'completed') bg-success
                        @elseif($order->status === 'cancelled') bg-danger
                        @else bg-secondary @endif">
                                {{ ucfirst($order->status) }}
                            </span>
                        </p>
                        <p>
                            <strong>💰 Tổng tiền:</strong>
                            <span class="text-danger fw-bold">{{ number_format($order->total_price, 0, ',', '.') }}
                                VNĐ</span>
                        </p>
                        <p><strong>🕒 Ngày tạo:</strong> {{ $order->created_at->format('d-m-Y H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <br>
        <h5 class="mt-4 mb-3">🛒 Sản phẩm trong đơn hàng</h5>

        <table class="table table-hover table-bordered align-middle text-center">
            <thead class="table-primary">
                <tr>
                    <th scope="col">Hình ảnh</th>
                    <th scope="col">Tên sản phẩm</th>
                    <th scope="col">Số lượng</th>
                    <th scope="col">Size</th>
                    <th scope="col">Đơn giá</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order->orderItems as $item)
                    <tr>
                        <td>
                            @php
                                $image = $item->productVariant->product->image ?? 'images/default-product.jpg';
                            @endphp
                            <img src="{{ Storage::url($image) }}" alt="Hình ảnh sản phẩm" class="img-thumbnail"
                                style="width: 100px; height: auto;">
                        </td>
                        <td class="text-start">
                            <strong>{{ $item->productVariant->product->name }}</strong>
                        </td>
                        <td>{{ $item->quantity }}</td>
                        <td>
                            <span class="badge bg-info text-dark">{{ $item->productVariant->size }}</span>
                        </td>
                        <td class="text-danger fw-bold">
                            {{ number_format($item->price, 0, ',', '.') }} VND
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>



        <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">Quay lại</a>
    </div>
@endsection
