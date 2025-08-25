@extends('layouts.admin')

@section('title', 'Quản lý đơn hàng')

@section('content')
    <div class="container-fluid">
        <h4 class="mb-4">Danh sách đơn hàng</h4>
        <form action="{{ route('admin.orders.index') }}" method="GET" class="row mb-3">
            <div class="col-md-3">
                <input type="text" name="keyword" class="form-control" placeholder="🔍 Tìm theo tên, email, mã đơn hàng..."
                    value="{{ request('keyword') }}">
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select">
                    <option value="">Tất cả trạng thái</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ xử lý</option>
                    <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Đang xử lý</option>
                    <option value="shipping" {{ request('status') == 'shipping' ? 'selected' : '' }}>Đang giao</option>
                    <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Đã giao</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Hoàn thành</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="payment_status" class="form-select">
                    <option value="">Tất cả thanh toán</option>
                    <option value="unpaid" {{ request('payment_status') == 'unpaid' ? 'selected' : '' }}>Chưa thanh toán</option>
                    <option value="awaiting_payment" {{ request('payment_status') == 'awaiting_payment' ? 'selected' : '' }}>Chờ thanh toán</option>
                    <option value="cod" {{ request('payment_status') == 'cod' ? 'selected' : '' }}>COD</option>
                    <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Đã thanh toán</option>
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-search"></i> Tìm kiếm
                </button>
            </div>
            <div class="col-auto">
                <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-clockwise"></i> Làm mới
                </a>
            </div>
        </form>
        
        <!-- Quick filter buttons -->
        <div class="mb-3">
            <a href="{{ route('admin.orders.awaiting-payment') }}" class="btn btn-outline-warning me-2">
                <i class="bi bi-clock"></i> Chờ thanh toán
            </a>
            <a href="{{ route('admin.orders.unpaid') }}" class="btn btn-outline-danger me-2">
                <i class="bi bi-exclamation-triangle"></i> Chưa thanh toán
            </a>
        </div>

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
                    <th>Trạng thái đơn hàng</th>
                    <th>Trạng thái thanh toán</th>
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
                        <td>
                            <span class="badge bg-{{ $order->status == 'pending' ? 'warning' : ($order->status == 'processing' ? 'info' : ($order->status == 'completed' ? 'success' : 'secondary')) }}">
                                {{ ucfirst($order->status) }}
                            </span>
                        </td>
                        <td>
                            @if($order->payment_status == 'paid')
                                <span class="badge bg-success">Đã thanh toán</span>
                            @elseif($order->payment_status == 'awaiting_payment')
                                <span class="badge bg-warning">Chờ thanh toán</span>
                            @elseif($order->payment_status == 'cod')
                                <span class="badge bg-info">COD</span>
                            @else
                                <span class="badge bg-secondary">Chưa thanh toán</span>
                            @endif
                        </td>
                        <td>{{ number_format($order->final_total ?? $order->total_price, 0, ',', '.') }} VNĐ</td>
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
                                @if ($order->status === 'completed')
                                    <a href="{{ route('admin.invoices.show', $order->id) }}"
                                        class="btn btn-sm btn-secondary">
                                        🧾 Hóa đơn
                                    </a>
                                @endif

                                {{-- Nút đánh dấu đã thanh toán đã được bỏ - thanh toán sẽ tự động khi chuyển sang trạng thái "Đã nhận" --}}

                            </div>
                        </td>



                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">Không có đơn hàng nào</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{ $orders->links() }}
    </div>
@endsection
