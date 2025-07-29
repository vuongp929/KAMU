@extends('layouts.admin')

@section('content')
<div class="container my-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>🧾 Hóa đơn #{{ $order->id }}</h4>
        <button onclick="window.print()" class="btn btn-outline-primary"><i class="bi bi-printer"></i> In hóa đơn</button>
    </div>

    <div class="card p-3">
        <h5>Thông tin khách hàng</h5>
        <p>
            <strong>Tên:</strong> {{ $order->customer?->name }} <br>
            <strong>Email:</strong> {{ $order->customer?->email }} <br>
            <strong>Ngày mua:</strong> {{ $order->created_at->format('d-m-Y H:i') }}
        </p>

        <h5 class="mt-4">Chi tiết đơn hàng</h5>
        <table class="table table-bordered">
            <thead class="table-light">
                <tr>
                    <th>Sản phẩm</th>
                    <th>Size</th>
                    <th>Số lượng</th>
                    <th>Đơn giá</th>
                    <th>Thành tiền</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order->orderItems as $item)
                    <tr>
                        <td>{{ $item->productVariant->product->name ?? '---' }}</td>
                        <td>{{ $item->productVariant->size }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ number_format($item->price, 0, ',', '.') }} VND</td>
                        <td>{{ number_format($item->quantity * $item->price, 0, ',', '.') }} VND</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="4" class="text-end">Tổng tiền:</th>
                    <th>{{ number_format($order->total_price, 0, ',', '.') }} VND</th>
                </tr>
            </tfoot>
        </table>

        <div class="text-end mt-4">
            <em>Cảm ơn bạn đã mua hàng!</em>
        </div>
    </div>
</div>
@endsection
