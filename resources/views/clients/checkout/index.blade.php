@extends('layouts.client')

@section('content')
<div class="container">
    <h2>Đơn hàng của bạn</h2>

    @if($orders->isEmpty())
        <p>Không có đơn hàng nào.</p>
    @else
        <table class="table table-bordered">
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
                {{-- @foreach($orders as $order)
                <tr>
                    <td>{{ $order->id }}</td>
                    <td>{{ number_format($order->total_price, 0, ',', '.') }} VND</td>
                    <td>{{ $order->status }}</td>
                    <td>{{ $order->payment_status }}</td>
                    <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                    <td>
                        <a href="{{ route('client.orders.show', $order->id) }}" class="btn btn-info">Xem chi tiết</a>
                   
                <!-- Hiển thị nút hủy nếu trạng thái là "Đang chờ xử lý" -->
                <a href="{{ route('client.orders.cancel', $order->id) }}" class="btn btn-danger" onclick="return confirm('Bạn có chắc chắn muốn hủy đơn hàng này?')">Hủy đơn hàng</a>
        
                    </td>
                </tr>
                @endforeach --}}
            </tbody>
        </table>
    @endif
</div>
@endsection
