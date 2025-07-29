@extends('layouts.admin')

@section('title', 'Quản lý đơn hàng')

@section('content')
    <div class="container-fluid">
        <h4 class="mb-4">Danh sách đơn hàng</h4>
        <form action="{{ route('admin.orders.index') }}" method="GET" class="row mb-3">
            <div class="col-md-4">
                <input type="text" name="keyword" class="form-control" placeholder="🔍 Tìm theo tên, email, mã đơn hàng..."
                    value="{{ request('keyword') }}">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-search"></i> Tìm kiếm
                </button>
            </div>
        </form>

        {{-- @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Đóng"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Đóng"></button>
            </div>
        @endif

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Mã đơn hàng</th>
                    <th>Khách hàng</th>
                    <th>Trạng thái</th>
                    <th>Tổng tiền</th>
                    <th>Ngày tạo</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                    <tr>
                        <td>{{ $order->id }}</td>
                        <td>{{ $order->customer?->name ?? 'Không xác định' }}</td>
                        <td>{{ $order->status }}</td>
                        <td>{{ number_format($order->total_price, 0, ',', '.') }} VNĐ</td>
                        <td>{{ $order->created_at->format('d-m-Y') }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">

                                {{-- Nút xem --}}
                                <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-sm btn-outline-info">
                                    👁️ Xem
                                </a>

                                {{-- Form thay đổi trạng thái --}}
                                <form action="{{ route('admin.orders.update', $order->id) }}" method="POST"
                                    style="min-width: 180px;">
                                    @csrf
                                    @method('PUT')

                                    <select name="status" class="form-select form-select-sm" onchange="this.form.submit()"
                                        @if (in_array($order->status, ['completed', 'cancelled']))  @endif>
                                        <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>🕓 Đang
                                            chờ thanh toán</option>
                                        <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>
                                            📦 Chờ duyệt đơn</option>
                                        <option value="shipping" {{ $order->status == 'shipping' ? 'selected' : '' }}>🚚
                                            Đang giao</option>
                                        <option value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }}>📬
                                            Đã giao</option>
                                        <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>✅
                                            Đã nhận</option>
                                        <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>❌
                                            Đã hủy</option>
                                    </select>
                                </form>

                            </div>
                        </td>



                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">Không có đơn hàng nào</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{ $orders->links() }}
    </div>
@endsection
