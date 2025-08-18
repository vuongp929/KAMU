@extends('layouts.admin')

@section('title', 'Chi tiết đơn hàng')

@section('content')
    <div class="container-fluid">
        <h4 class="mb-4">Chi tiết đơn hàng #{{ $order->id }}</h4>

        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Thông tin khách hàng</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Khách hàng:</strong> {{ $order->customer->name ?? 'N/A' }}</p>
                        <p><strong>Email:</strong> {{ $order->customer->email ?? 'N/A' }}</p>
                        <p><strong>Địa chỉ giao hàng:</strong> {{ $order->shipping_address ?? 'N/A' }}</p>
                        <p><strong>Phương thức thanh toán:</strong> 
                            @if($order->payment_method == 'cod')
                                Thanh toán khi nhận hàng (COD)
                            @elseif($order->payment_method == 'vnpay')
                                Thanh toán qua VNPAY
                            @else
                                {{ $order->payment_method ?? 'N/A' }}
                            @endif
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Thông tin đơn hàng</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Trạng thái đơn hàng:</strong> 
                            <span class="badge bg-{{ $order->status == 'pending' ? 'warning' : ($order->status == 'processing' ? 'info' : ($order->status == 'completed' ? 'success' : 'secondary')) }}">
                                {{ ucfirst($order->status) }}
                            </span>
                        </p>
                        <p><strong>Trạng thái thanh toán:</strong> 
                            <span class="badge bg-{{ $order->payment_status == 'paid' ? 'success' : 'warning' }}">
                                {{ $order->payment_status == 'paid' ? 'Đã thanh toán' : 'Chưa thanh toán' }}
                            </span>
                        </p>
                        <p><strong>Ngày tạo:</strong> {{ $order->created_at->format('d-m-Y H:i') }}</p>
                        <p><strong>Cập nhật lần cuối:</strong> {{ $order->updated_at->format('d-m-Y H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Sản phẩm trong đơn hàng</h5>
            </div>
            <div class="card-body">
                @if($order->orderItems && $order->orderItems->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th width="80">Hình ảnh</th>
                                    <th>Tên sản phẩm</th>
                                    <th width="100">Phân loại</th>
                                    <th width="80">Số lượng</th>
                                    <th width="120">Giá gốc</th>
                                    <th width="120">Giá đặt hàng</th>
                                    <th width="120">Thành tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $subtotal = 0;
                                    $totalDiscount = 0;
                                @endphp
                                @foreach($order->orderItems as $item)
                                    @php
                                        $originalPrice = $item->price ?? 0;
                                        $orderPrice = $item->price_at_order ?? $originalPrice;
                                        $itemTotal = $item->quantity * $orderPrice;
                                        $itemDiscount = $item->quantity * ($originalPrice - $orderPrice);
                                        $subtotal += $itemTotal;
                                        $totalDiscount += $itemDiscount;
                                    @endphp
                                    <tr>
                                        <td>
                                            @if($item->variant && $item->variant->product && $item->variant->product->thumbnail_url)
                                                <img src="{{ $item->variant->product->thumbnail_url }}" 
                                                     alt="{{ $item->variant->product->name }}" 
                                                     class="img-thumbnail" 
                                                     style="width: 60px; height: 60px; object-fit: cover;">
                                            @else
                                                <img src="{{ asset('images/default-product.jpg') }}" 
                                                     alt="Hình ảnh mặc định" 
                                                     class="img-thumbnail" 
                                                     style="width: 60px; height: 60px; object-fit: cover;">
                                            @endif
                                        </td>
                                        <td>
                                            <strong>{{ $item->variant->product->name ?? 'Sản phẩm không tồn tại' }}</strong>
                                            @if($item->variant)
                                                <br><small class="text-muted">Phân loại: {{ $item->variant->name ?? 'N/A' }}</small>
                                            @endif
                                        </td>
                                        <td class="text-center">{{ $item->variant ? $item->variant->name : 'N/A' }}</td>
                                        <td class="text-center">{{ $item->quantity }}</td>
                                        <td class="text-end">
                                            @if($originalPrice > $orderPrice)
                                                <span class="text-decoration-line-through text-muted">
                                                    {{ number_format($originalPrice, 0, ',', '.') }}đ
                                                </span>
                                            @else
                                                {{ number_format($originalPrice, 0, ',', '.') }}đ
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            {{ number_format($orderPrice, 0, ',', '.') }}đ
                                            @if($originalPrice > $orderPrice)
                                                <br><small class="text-success">Giảm {{ number_format($originalPrice - $orderPrice, 0, ',', '.') }}đ</small>
                                            @endif
                                        </td>
                                        <td class="text-end fw-bold">
                                            {{ number_format($itemTotal, 0, ',', '.') }}đ
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Tóm tắt đơn hàng -->
                    <div class="row justify-content-end">
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">Tóm tắt đơn hàng</h6>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Tạm tính:</span>
                                        <span>{{ number_format($subtotal, 0, ',', '.') }}đ</span>
                                    </div>
                                    @php
                                        // Tính toán số tiền giảm giá từ tổng tiền gốc và tổng tiền cuối
                                        $originalTotal = $subtotal;
                                        $finalTotal = $order->final_total ?? $order->total_price; // Ưu tiên final_total
                                        $orderDiscount = $originalTotal - $finalTotal;
                                    @endphp
                                    
                                    {{-- Hiển thị thông tin voucher nếu có --}}
                                    @if($order->discount_code && $order->discount_amount > 0)
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Mã giảm giá ({{ $order->discount_code }}):</span>
                                            <span class="text-success">-{{ number_format($order->discount_amount, 0, ',', '.') }}đ</span>
                                        </div>
                                    @elseif($orderDiscount > 0)
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Giảm giá:</span>
                                            <span class="text-success">-{{ number_format($orderDiscount, 0, ',', '.') }}đ</span>
                                        </div>
                                    @endif
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Phí vận chuyển:</span>
                                        <span>Miễn phí</span>
                                    </div>
                                    <hr>
                                    <div class="d-flex justify-content-between fw-bold">
                                        <span>Tổng cộng:</span>
                                        <span class="text-primary fs-5">{{ number_format($order->final_total ?? $order->total_price, 0, ',', '.') }}đ</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Không có sản phẩm nào trong đơn hàng này.
                    </div>
                    
                    <!-- Fallback: Hiển thị dữ liệu từ JSON cart nếu có -->
                    @if($order->cart)
                        <div class="alert alert-info">
                            <strong>Thử hiển thị từ dữ liệu cart:</strong>
                        </div>
        @php
            $cartItems = json_decode($order->cart, true);
        @endphp
        @if (!empty($cartItems))
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead class="table-dark">
                    <tr>
                                            <th width="80">Hình ảnh</th>
                        <th>Tên sản phẩm</th>
                                            <th width="80">Số lượng</th>
                                            <th width="120">Giá</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($cartItems as $item)
                        <tr>
                            <td>
                                @if (isset($item['image']))
                                                        <img src="{{ Storage::url($item['image']) }}" alt="Hình ảnh sản phẩm" class="img-thumbnail" style="width: 60px; height: 60px; object-fit: cover;">
                                @else
                                                        <img src="{{ asset('images/default-product.jpg') }}" alt="Hình ảnh mặc định" class="img-thumbnail" style="width: 60px; height: 60px; object-fit: cover;">
                                @endif
                            </td>
                                                <td>{{ $item['name'] ?? 'N/A' }}</td>
                                                <td class="text-center">{{ $item['quantity'] ?? 0 }}</td>
                                                <td class="text-end">{{ number_format($item['price'] ?? 0, 0, ',', '.') }}đ</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
                            </div>
                        @endif
                    @endif
        @endif
            </div>
        </div>

        <div class="mt-4">
            <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Quay lại danh sách
            </a>
            <button class="btn btn-primary" onclick="window.print()">
                <i class="fas fa-print me-2"></i>In đơn hàng
            </button>
        </div>
    </div>

    <style>
        @media print {
            .btn, .card-header {
                display: none !important;
            }
            .card {
                border: none !important;
                box-shadow: none !important;
            }
        }
    </style>
@endsection
