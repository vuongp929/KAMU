@extends('layouts.client')

@php
    function getOrderStatusText($status) {
        switch($status) {
            case 'pending':
                return 'CHỜ XỬ LÝ';
            case 'processing':
                return 'ĐANG XỬ LÝ';
            case 'completed':
                return 'HOÀN THÀNH';
            case 'cancelled':
                return 'ĐÃ HỦY';
            default:
                return strtoupper($status);
        }
    }
@endphp
@section('title', 'Đơn hàng của tôi')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
<style>
    /* Custom CSS for Shopee-like Order Page */
    body { background-color: #f5f5f5; }
    .profile-layout { background-color: #fff; border-radius: 4px; box-shadow: 0 1px 2px 0 rgba(0,0,0,.13); }
    /* Sidebar */
    .profile-sidebar { padding: 25px; border-right: 1px solid #efefef; }
    .user-info { display: flex; align-items: center; padding-bottom: 20px; border-bottom: 1px solid #efefef; }
    .user-avatar { width: 50px; height: 50px; border-radius: 50%; background-color: #eee; margin-right: 15px; text-align: center; line-height: 50px; font-size: 24px; color: #aaa; }
    .user-name { font-weight: 600; color: #333; }
    .user-edit a { font-size: 14px; color: #888; text-decoration: none; }
    .user-edit a:hover { color: #007bff; }
    .sidebar-nav { margin-top: 20px; }
    .sidebar-nav .nav-link { color: #555; padding: 10px 0; font-size: 15px; display: flex; align-items: center; }
    .sidebar-nav .nav-link i { margin-right: 10px; width: 20px; text-align: center; }
    .sidebar-nav .nav-link.active, .sidebar-nav .nav-link:hover { color: #007bff; font-weight: 600; }
    
    /* Main Content */
    .profile-content { padding: 0 30px 30px 30px; }
    .order-tabs .nav-link { color: #666; font-size: 16px; border: none; border-bottom: 2px solid transparent; }
    .order-tabs .nav-link.active { color: #007bff; border-bottom-color: #007bff; font-weight: 600; }
    .order-card { background: #fff; border: 1px solid #e8e8e8; margin-bottom: 15px; border-radius: 4px; }
    .order-header, .order-footer { padding: 15px 20px; background-color: #fafafa; }
    .order-header { border-bottom: 1px solid #e8e8e8; display: flex; justify-content: space-between; align-items: center; }
    .order-status { font-weight: 600; color: #007bff; text-transform: uppercase; }
    .order-item { padding: 20px; display: flex; align-items: center; border-bottom: 1px solid #f0f0f0; }
    .order-item:last-child { border-bottom: none; }
    .order-item-img { width: 80px; height: 80px; margin-right: 15px; border: 1px solid #eee; }
    .order-item-info { flex-grow: 1; }
    .order-item-name { color: #333; font-weight: 500; margin-bottom: 5px; }
    .order-item-variant, .order-item-qty { color: #777; font-size: 14px; }
    .order-item-price { font-weight: 500; color: #333; text-align: right; }
    .order-item-discount { font-size: 12px; }
    .order-footer { border-top: 1px solid #e8e8e8; padding: 15px 20px; }
    .total-price { font-size: 18px; font-weight: 600; color: #007bff; }
    .order-actions .btn { margin-left: 10px; }
    .text-decoration-line-through { text-decoration: line-through; }
    .status-badge { padding: 6px 12px; border-radius: 20px; font-size: 0.8em; font-weight: bold; }
    .status-pending { background: #fff3cd; color: #856404; }
    .status-processing { background: #d1ecf1; color: #0c5460; }
    .status-completed { background: #d4edda; color: #155724; }
    .status-cancelled { background: #f8d7da; color: #721c24; }
</style>

<script>
function confirmCancelOrder(orderId) {
    if (confirm('Bạn có chắc chắn muốn hủy đơn hàng này?')) {
        // Tạo form để submit
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("client.orders.cancel", ":orderId") }}'.replace(':orderId', orderId);
        
        // Thêm CSRF token
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);
        
        // Thêm method override
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'PATCH';
        form.appendChild(methodField);
        
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endpush

@section('content')
<div class="container my-5">
    <div class="row">
        {{-- Bắt đầu cột Sidebar bên trái --}}
        <div class="col-md-3">
            <div class="profile-sidebar">
                <div class="user-info">
                    <div class="user-avatar"><i class="fas fa-user"></i></div>
                    <div>
                        <div class="user-name">{{ Auth::user()->name }}</div>
                        <div class="user-edit">
                            <a href="{{ route('profile.edit') }}"><i class="fas fa-pencil-alt"></i> Sửa Hồ Sơ</a>
                        </div>
                    </div>
                </div>
                <ul class="nav flex-column sidebar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('profile.edit') }}"><i class="fas fa-user"></i> Tài Khoản Của Tôi</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('client.orders.index') }}"><i class="fas fa-clipboard-list"></i> Đơn Mua</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('client.rewards.index') }}"><i class="fas fa-star"></i> Điểm Thưởng</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('client.rewards.discount-codes') }}"><i class="fas fa-ticket-alt"></i> Mã Đổi Thưởng</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="fas fa-bell"></i> Thông Báo</a>
                    </li>
                </ul>
            </div>
        </div>
        {{-- Kết thúc cột Sidebar --}}

        {{-- Bắt đầu cột Nội dung chính bên phải --}}
        <div class="col-md-9">
            <div class="profile-content">
                {{-- Các Tab lọc đơn hàng --}}
                <ul class="nav nav-tabs order-tabs">
                    <li class="nav-item">
                        <a class="nav-link {{ request('status') == '' ? 'active' : '' }}" href="{{ route('client.orders.index') }}">Tất cả</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request('status') == 'pending' ? 'active' : '' }}" href="{{ route('client.orders.index', ['status' => 'pending']) }}">Chờ thanh toán</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request('status') == 'processing' ? 'active' : '' }}" href="{{ route('client.orders.index', ['status' => 'processing']) }}">Vận chuyển</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request('status') == 'completed' ? 'active' : '' }}" href="{{ route('client.orders.index', ['status' => 'completed']) }}">Hoàn thành</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request('status') == 'cancelled' ? 'active' : '' }}" href="{{ route('client.orders.index', ['status' => 'cancelled']) }}">Đã hủy</a>
                    </li>
                </ul>

                <div class="mt-4">
                    {{-- Lặp qua từng đơn hàng --}}
                    @forelse($orders as $order)
                        <div class="order-card">
                            <div class="order-header">
                                <span>Mã đơn hàng: #{{ $order->id }}</span>
                                <span class="status-badge status-{{ $order->status }}">
                                    {{ getOrderStatusText($order->status) }}
                                </span>
                            </div>
                            <div class="order-body">
                                {{-- Lặp qua từng sản phẩm trong đơn hàng --}}
                                @php
                                    $orderSubtotal = 0;
                                    $orderDiscount = 0;
                                @endphp
                                @foreach($order->orderItems as $item)
                                    @if($item->productVariant && $item->productVariant->product)
                                        @php
                                            $originalPrice = $item->price ?? 0;
                                            $orderPrice = $item->price_at_order ?? $originalPrice;
                                            $itemTotal = $item->quantity * $orderPrice;
                                            $itemDiscount = $item->quantity * ($originalPrice - $orderPrice);
                                            $orderSubtotal += $itemTotal;
                                            $orderDiscount += $itemDiscount;
                                        @endphp
                                        <div class="order-item">
                                            <img src="{{ optional($item->productVariant->product)->thumbnail_url }}" class="order-item-img" alt="">
                                            <div class="order-item-info">
                                                <div class="order-item-name">{{ $item->productVariant->product->name }}</div>
                                                <div class="order-item-variant">Phân loại hàng: {{ $item->productVariant->name }}</div>
                                                <div class="order-item-qty">x{{ $item->quantity }}</div>
                                                @if($originalPrice > $orderPrice)
                                                    <div class="order-item-discount text-success">
                                                        <small>Giảm {{ number_format($originalPrice - $orderPrice, 0, ',', '.') }}đ/sản phẩm</small>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="order-item-price">
                                                @if($originalPrice > $orderPrice)
                                                    <div class="text-decoration-line-through text-muted">
                                                        <small>{{ number_format($originalPrice, 0, ',', '.') }}đ</small>
                                                    </div>
                                                @endif
                                                <div class="fw-bold">{{ number_format($orderPrice, 0, ',', '.') }}đ</div>
                                                <div class="text-end fw-bold text-primary">
                                                    {{ number_format($itemTotal, 0, ',', '.') }}đ
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="order-item">
                                            <div class="order-item-info text-danger">
                                                <p><strong>Sản phẩm này không còn tồn tại hoặc đã bị lỗi dữ liệu.</strong></p>
                                                <small>(ID Biến thể: {{ $item->product_variant_id }})</small>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                            <div class="order-footer">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="me-3">Thành tiền:</span>
                                        <span class="total-price">{{ number_format($order->total_price, 0, ',', '.') }}đ</span>
                                        @if($order->payment_status == 'paid')
                                            <span class="badge badge-success ml-2">
                                                <i class="fas fa-star"></i> +20 điểm
                                            </span>
                                        @endif
                                    </div>
                                    <div class="order-actions">
                                        @if($order->status == 'delivered')
                                            <form action="{{ route('client.orders.complete', $order) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-success btn-sm">
                                                    <i class="fas fa-check"></i> Hoàn Thành
                                                </button>
                                            </form>
                                        @endif
                                        <a href="{{ route('client.orders.show', $order) }}" class="btn btn-primary btn-sm">
                                            <i class="fas fa-eye"></i> Chi Tiết
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center p-5">
                            <p>Chưa có đơn hàng nào.</p>
                        </div>
                    @endforelse
                </div>

                {{-- Phân trang --}}
                <div class="d-flex justify-content-center mt-4">
                    {{ $orders->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
        {{-- Kết thúc cột Nội dung chính --}}
    </div>
</div>
@endsection
