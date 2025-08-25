@extends('layouts.client')

@php
    function getOrderStatusText($status) {
        switch($status) {
            case 'pending':
                return 'CHỜ XỬ LÝ';
            case 'processing':
                return 'ĐANG XỬ LÝ';
            case 'shipping':
                return 'ĐANG GIAO HÀNG';
            case 'delivered':
                return 'ĐÃ GIAO HÀNG';
            case 'completed':
                return 'HOÀN THÀNH';
            case 'cancelled':
                return 'ĐÃ HỦY';
            default:
                return strtoupper($status);
        }
    }
@endphp
@section('title', 'Chi tiết đơn hàng')

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
    <style>
        body {
            background-color: #f5f5f5;
        }

        .order-detail-container {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .order-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 8px 8px 0 0;
        }

        .order-info {
            padding: 20px;
        }

        .info-section {
            margin-bottom: 30px;
        }

        .info-section h5 {
            color: #333;
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding: 10px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .info-label {
            font-weight: 600;
            color: #555;
        }

        .info-value {
            color: #333;
        }

        .product-item {
            display: flex;
            align-items: center;
            padding: 15px;
            border: 1px solid #e8e8e8;
            border-radius: 5px;
            margin-bottom: 10px;
        }

        .product-image {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 5px;
            margin-right: 15px;
        }

        .product-info {
            flex-grow: 1;
        }

        .product-name {
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
        }

        .product-variant {
            color: #666;
            font-size: 14px;
            margin-bottom: 5px;
        }

        .product-price {
            font-weight: 600;
            color: #007bff;
        }

        .status-badge {
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-processing {
            background-color: #d1ecf1;
            color: #0c5460;
        }

        .status-shipping {
            background-color: #cce5ff;
            color: #004085;
        }

        .status-completed {
            background-color: #d4edda;
            color: #155724;
        }

        .status-delivered {
            background-color: #e2e3e5;
            color: #383d41;
        }

        .status-cancelled {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .summary-card {
            background-color: #fff;
            border: 1px solid #e8e8e8;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }
        
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding: 8px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .summary-total {
            border-top: 2px solid #007bff;
            padding-top: 15px;
            margin-top: 15px;
            border-bottom: none;
        }
        
        .order-stats {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
        }
        
        .stat-item {
            display: flex;
            align-items: center;
        }
    </style>
@endpush

@section('content')
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="order-detail-container">
                    <!-- Header -->
                    <div class="order-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="mb-1">Đơn Hàng #{{ $order->id }}</h3>
                                <p class="mb-0">Đặt ngày: {{ $order->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                            <div class="text-right">
                                <span class="status-badge status-{{ $order->status }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="order-info">
                        <!-- Thông tin khách hàng -->
                        <div class="info-section">
                            <h5><i class="fas fa-user"></i> Thông Tin Khách Hàng</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="info-row">
                                        <span class="info-label">Tên khách hàng:</span>
                                        <span class="info-value">{{ $order->customer_name }}</span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">Email:</span>
                                        <span class="info-value">{{ $order->customer_email }}</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-row">
                                        <span class="info-label">Số điện thoại:</span>
                                        <span class="info-value">{{ $order->customer_phone }}</span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">Địa chỉ giao hàng:</span>
                                        <span class="info-value">{{ $order->customer_address }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                             @php
                                 // Tính toán subtotal từ orderItems (sử dụng giá tại thời điểm đặt hàng)
                                 $subtotal = 0;
                                 foreach ($order->orderItems as $item) {
                                     if ($item->productVariant && $item->productVariant->product) {
                                         $originalPrice = $item->price ?? 0;
                                         $orderPrice = $item->price_at_order ?? $originalPrice;
                                         $itemTotal = $item->quantity * $orderPrice;
                                         $subtotal += $itemTotal;
                                     }
                                 }
                             @endphp
                             <!-- Order Summary -->
                             <div class="row mt-4">
                                 <div class="col-md-8">
                                     <div class="summary-card p-4">
                                         <h5 class="mb-4 text-primary"><i class="fas fa-calculator me-2"></i>Tóm tắt đơn hàng</h5>
                                         <div class="row">
                                             <div class="col-md-6">
                                                 <div class="summary-row">
                                                     <span class="fw-bold">Tạm tính:</span>
                                                     <span class="text-end">{{ number_format($subtotal, 0, ',', '.') }}đ</span>
                                                 </div>
                                                 @php
                                                     // Tính toán số tiền giảm giá từ tổng tiền gốc và tổng tiền cuối
                                                     $originalTotal = $subtotal;
                                                     $finalTotal = $order->final_total ?? $order->total_price; // Ưu tiên final_total
                                                     $orderDiscount = $originalTotal - $finalTotal;
                                                 @endphp
                                                 
                                                 {{-- Hiển thị thông tin voucher nếu có --}}
                                                 @if($order->discount_code && $order->discount_amount > 0)
                                                     <div class="summary-row">
                                                         <span class="fw-bold text-success">Mã giảm giá ({{ $order->discount_code }}):</span>
                                                         <span class="text-success text-end fw-bold">-{{ number_format($order->discount_amount, 0, ',', '.') }}đ</span>
                                                     </div>
                                                 @elseif($orderDiscount > 0)
                                                     <div class="summary-row">
                                                         <span class="fw-bold text-success">Giảm giá:</span>
                                                         <span class="text-success text-end fw-bold">-{{ number_format($orderDiscount, 0, ',', '.') }}đ</span>
                                                     </div>
                                                 @endif
                                                 <div class="summary-row">
                                                     <span class="fw-bold">Phí vận chuyển:</span>
                                <span class="text-success text-end">{{ $order->shipping_fee > 0 ? number_format($order->shipping_fee, 0, ',', '.') . ' VNĐ' : 'Miễn phí' }}</span>
                                                 </div>
                                                 <div class="summary-row summary-total">
                                                     <span class="fw-bold fs-5">Tổng cộng:</span>
                                                     <span class="text-primary fw-bold fs-5">{{ number_format($order->final_total ?? $order->total_price, 0, ',', '.') }}đ</span>
                                                 </div>
                                             </div>
                                             <div class="col-md-6">
                                                 <div class="order-stats">
                                                     <h6 class="text-muted mb-3">Thống kê đơn hàng</h6>
                                                     <div class="stat-item mb-2">
                                                         <i class="fas fa-box me-2 text-primary"></i>
                                                         <span>Số sản phẩm: <strong>{{ $order->orderItems->count() }}</strong></span>
                                                     </div>
                                                     <div class="stat-item mb-2">
                                                         <i class="fas fa-shopping-cart me-2 text-success"></i>
                                                         <span>Tổng số lượng: <strong>{{ $order->orderItems->sum('quantity') }}</strong></span>
                                                     </div>
                                                                                                           <div class="stat-item mb-2">
                                                          <i class="fas fa-clock me-2 text-warning"></i>
                                                          <span>Trạng thái: 
                                                              <span class="status-badge status-{{ $order->status }}">
                                                                  {{ getOrderStatusText($order->status) }}
                                                              </span>
                                                          </span>
                                                      </div>
                                                     <div class="stat-item">
                                                         <i class="fas fa-calendar me-2 text-info"></i>
                                                         <span>Ngày đặt: <strong>{{ $order->created_at->format('d/m/Y') }}</strong></span>
                                                     </div>
                                                 </div>
                                             </div>
                                         </div>
                                     </div>
                                 </div>
                             </div>
                        
                        <!-- Thông tin đơn hàng -->
                        <div class="info-section">
                            <h5><i class="fas fa-shopping-cart"></i> Thông Tin Đơn Hàng</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="info-row">
                                        <span class="info-label">Mã đơn hàng:</span>
                                        <span class="info-value">#{{ $order->id }}</span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">Phương thức thanh toán:</span>
                                        <span class="info-value">{{ strtoupper($order->payment_method) }}</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-row">
                                        <span class="info-label">Trạng thái thanh toán:</span>
                                        <span class="info-value">
                                            @if($order->payment_status == 'paid')
                                                <span class="badge bg-success">Đã thanh toán</span>
                                            @elseif($order->payment_status == 'awaiting_payment')
                                                <span class="badge bg-warning">Chờ thanh toán</span>
                                                @if(in_array($order->payment_method, ['vnpay', 'momo']) && $order->status !== 'cancelled')
                                                    <div class="mt-2">
                                                        @if($order->payment_method == 'vnpay')
                                                            <a href="{{ route('payment.vnpay.create', ['orderId' => $order->id]) }}" class="btn btn-warning btn-sm">
                                                                <i class="fas fa-credit-card"></i> Tiếp tục thanh toán VNPay
                                                            </a>
                                                        @elseif($order->payment_method == 'momo')
                                                            <a href="{{ route('payment.momo.create', ['orderId' => $order->id]) }}" class="btn btn-warning btn-sm">
                                                                <i class="fas fa-credit-card"></i> Tiếp tục thanh toán MoMo
                                                            </a>
                                                        @endif
                                                    </div>
                                                @endif
                                            @elseif($order->payment_status == 'cod')
                                                <span class="badge bg-info">Thanh toán khi nhận hàng</span>
                                            @else
                                                <span class="badge bg-secondary">Chưa thanh toán</span>
                                            @endif
                                        </span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">Tổng tiền:</span>
                                        <span
                                            class="info-value text-success font-weight-bold">{{ number_format($order->final_total ?? $order->total_price) }}
                                            VND</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Danh sách sản phẩm -->
                        <div class="info-section">
                            <h5><i class="fas fa-box"></i> Sản Phẩm Đã Đặt</h5>
                            @if($order->orderItems->count() > 0)
                                @foreach ($order->orderItems as $item)
                                    @if ($item->productVariant && $item->productVariant->product)
                                        @php
                                            $originalPrice = $item->price ?? 0;
                                            $orderPrice = $item->price_at_order ?? $originalPrice;
                                            $itemTotal = $item->quantity * $orderPrice;
                                        @endphp
                                        <div class="product-item">
                                            <img src="{{ optional($item->productVariant->product)->thumbnail_url }}"
                                                class="product-image" alt="{{ $item->productVariant->product->name }}">
                                            <div class="product-info">
                                                <div class="product-name">{{ $item->productVariant->product->name }}</div>
                                                <div class="product-variant">Phân loại: {{ $item->productVariant->name }}</div>
                                                <div class="product-variant">Số lượng: {{ $item->quantity }}</div>
                                            </div>
                                            <div class="product-price">
                                                @if($originalPrice > $orderPrice)
                                                    <div class="text-decoration-line-through text-muted">
                                                        <small>{{ number_format($originalPrice, 0, ',', '.') }}đ</small>
                                                    </div>
                                                @endif
                                                <span>Giá sản phẩm:</span>
                                                <span>{{ number_format($orderPrice, 0, ',', '.') }}đ</span>
                                                @if($originalPrice > $orderPrice)
                                                    <div class="text-success">
                                                        <small>Giảm {{ number_format($originalPrice - $orderPrice, 0, ',', '.') }}đ</small>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @else
                                        <div class="product-item">
                                            <div class="product-info text-danger">
                                                <div class="product-name">Sản phẩm không còn tồn tại</div>
                                                <div class="product-variant">ID: {{ $item->product_variant_id }}</div>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            @else
                                <div class="text-center p-5">
                                    <i class="fas fa-exclamation-triangle text-warning mb-3" style="font-size: 3rem;"></i>
                                    <h5>Không có sản phẩm nào trong đơn hàng này.</h5>
                                </div>
                            @endif
                        </div>

                        <!-- Điểm thưởng -->
                        @if ($order->status == 'completed')
                            <div class="info-section">
                                <h5><i class="fas fa-star text-warning"></i> Điểm Thưởng</h5>
                                <div class="alert alert-success">
                                    <i class="fas fa-check-circle"></i>
                                    Bạn đã nhận được <strong>+20 điểm thưởng</strong> cho đơn hàng này!
                                </div>
                            </div>
                        @endif

                        <!-- Nút xác nhận thanh toán COD -->
                        @if ($order->payment_method == 'cod' && $order->payment_status == 'unpaid')
                            <div class="info-section">
                                <h5><i class="fas fa-money-bill-wave"></i> Xác Nhận Thanh Toán COD</h5>
                                <form action="{{ route('client.payment.cod', $order) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-check"></i> Xác Nhận Đã Thanh Toán
                                    </button>
                                    <small class="form-text text-muted">
                                        Nhấn nút này để xác nhận đã thanh toán khi nhận hàng và nhận điểm thưởng.
                                    </small>
                                </form>
                            </div>
                        @endif

                        <!-- Nút hủy đơn hàng -->
                        @if (in_array($order->status, ['pending', 'processing']))
                            <div class="info-section">
                                <h5><i class="fas fa-times-circle"></i> Hủy Đơn Hàng</h5>
                                <button type="button" class="btn btn-danger btn-lg" onclick="confirmCancelOrder({{ $order->id }})">
                                    <i class="fas fa-times"></i> Hủy Đơn Hàng
                                </button>
                                <small class="form-text text-muted">
                                    Bạn chỉ có thể hủy đơn hàng khi đơn hàng đang chờ xử lý hoặc đang xử lý.
                                </small>
                            </div>
                        @endif

                        <!-- Nút hoàn thành đơn hàng -->
                        @if ($order->status == 'delivered')
                            <div class="info-section">
                                <h5><i class="fas fa-check-circle"></i> Hoàn Thành Đơn Hàng</h5>
                                <form action="{{ route('client.orders.complete', $order) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-lg">
                                        <i class="fas fa-check"></i> Xác Nhận Hoàn Thành
                                    </button>
                                    <small class="form-text text-muted">
                                        Nhấn nút này để hoàn thành đơn hàng.
                                    </small>
                                </form>

                            </div>
                        @endif
                    </div>
                </div>

                <!-- Nút quay lại -->
                <div class="text-center mt-4">
                    <a href="{{ route('client.orders.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Quay Lại Danh Sách
                    </a>
                </div>
            </div>
        </div>
    </div>

</div>

<style>
@media print {
    .btn, .order-info-card, .col-md-3 { display: none !important; }
    .col-md-9 { width: 100% !important; }
    .order-detail-container { box-shadow: none !important; }
}
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
@endsection

