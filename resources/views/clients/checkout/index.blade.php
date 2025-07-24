@extends('layouts.client')

@section('content')
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
@endsection
