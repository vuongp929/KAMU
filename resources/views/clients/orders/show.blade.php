@extends('layouts.client')
@section('title', 'Chi tiết đơn hàng')

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

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
<style>
    body { background-color: #f5f5f5; }
    .order-detail-container { background-color: #fff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
    .order-header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; border-radius: 8px 8px 0 0; }
    .order-info-card { background: #fff; border: 1px solid #e9ecef; border-radius: 8px; margin-bottom: 20px; }
    .order-info-header { background: #f8f9fa; padding: 15px 20px; border-bottom: 1px solid #e9ecef; border-radius: 8px 8px 0 0; }
    .order-info-body { padding: 20px; }
    .product-table { background: #fff; border-radius: 8px; overflow: hidden; }
    .product-table th { background: #343a40; color: white; border: none; }
    .product-image { width: 60px; height: 60px; object-fit: cover; border-radius: 4px; }
    .price-original { text-decoration: line-through; color: #6c757d; font-size: 0.9em; }
    .price-discount { color: #28a745; font-size: 0.8em; }
    .price-final { font-weight: bold; color: #007bff; }
    .summary-card { background: #f8f9fa; border: 1px solid #e9ecef; border-radius: 8px; }
    .summary-row { display: flex; justify-content: space-between; padding: 8px 0; }
    .summary-total { border-top: 2px solid #dee2e6; font-weight: bold; font-size: 1.1em; color: #007bff; }
    .status-badge { padding: 6px 12px; border-radius: 20px; font-size: 0.8em; font-weight: bold; }
         .status-pending { background: #fff3cd; color: #856404; }
     .status-processing { background: #d1ecf1; color: #0c5460; }
     .status-completed { background: #d4edda; color: #155724; }
     .status-cancelled { background: #f8d7da; color: #721c24; }
     .order-stats { background: #f8f9fa; padding: 20px; border-radius: 8px; border-left: 4px solid #007bff; }
     .stat-item { display: flex; align-items: center; padding: 8px 0; border-bottom: 1px solid #e9ecef; }
     .stat-item:last-child { border-bottom: none; }
     .stat-item i { width: 20px; }
     .summary-card { background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border: 1px solid #dee2e6; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
     .summary-row { display: flex; justify-content: space-between; align-items: center; padding: 12px 0; border-bottom: 1px solid #e9ecef; }
     .summary-row:last-child { border-bottom: none; }
     .summary-total { border-top: 2px solid #007bff; margin-top: 10px; padding-top: 15px; }
     .product-table { border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
     .product-table th { background: linear-gradient(135deg, #343a40 0%, #495057 100%); color: white; border: none; padding: 15px 10px; }
     .product-table td { padding: 15px 10px; vertical-align: middle; }
     .product-image { width: 70px; height: 70px; object-fit: cover; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
     .price-original { text-decoration: line-through; color: #6c757d; font-size: 0.9em; }
     .price-discount { color: #28a745; font-size: 0.8em; font-weight: bold; }
     .price-final { font-weight: bold; color: #007bff; }
     .order-header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 25px; border-radius: 12px 12px 0 0; }
     .order-info-card { background: #fff; border: 1px solid #e9ecef; border-radius: 12px; margin-bottom: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); overflow: hidden; }
     .order-info-header { background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); padding: 18px 20px; border-bottom: 1px solid #e9ecef; }
     .order-info-body { padding: 25px; }
     .order-info-body p { margin-bottom: 12px; line-height: 1.6; }
     .order-info-body p:last-child { margin-bottom: 0; }
</style>
@endpush

@section('content')
<div class="container my-5">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3">
            <div class="order-info-card">
                <div class="order-info-header">
                    <h6 class="mb-0"><i class="fas fa-user me-2"></i>Thông tin tài khoản</h6>
                </div>
                <div class="order-info-body">
                    <div class="mb-3">
                        <strong>{{ Auth::user()->name }}</strong>
                    </div>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('profile.edit') }}">
                                <i class="fas fa-user me-2"></i>Tài khoản của tôi
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="{{ route('client.orders.index') }}">
                                <i class="fas fa-clipboard-list me-2"></i>Đơn hàng của tôi
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-md-9">
            <div class="order-detail-container">
                                 <!-- Order Header -->
                 <div class="order-header">
                     <div class="row align-items-center">
                         <div class="col-md-10">
                             <h4 class="mb-0">Đơn hàng #{{ $order->id }}</h4>
                             <small>Tạo lúc: {{ $order->created_at->format('d/m/Y H:i') }}</small>
                         </div>
                         <div class="col-md-2 text-end">
                             <span class="status-badge status-{{ $order->status }}">
                                 {{ getOrderStatusText($order->status) }}
                             </span>
                         </div>
                     </div>
                 </div>

                <!-- Order Information -->
                <div class="p-4">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="order-info-card">
                                <div class="order-info-header">
                                    <h6 class="mb-0"><i class="fas fa-shipping-fast me-2"></i>Thông tin giao hàng</h6>
                                </div>
                                <div class="order-info-body">
                                    <p><strong>Địa chỉ:</strong> {{ $order->shipping_address ?? 'N/A' }}</p>
                                    <p><strong>Phương thức thanh toán:</strong> 
                                        @if($order->payment_method == 'cod')
                                            Thanh toán khi nhận hàng (COD)
                                        @elseif($order->payment_method == 'vnpay')
                                            Thanh toán qua VNPAY
                                        @else
                                            {{ $order->payment_method ?? 'N/A' }}
                                        @endif
                                    </p>
                                    <p><strong>Trạng thái thanh toán:</strong> 
                                        <span class="badge bg-{{ $order->payment_status == 'paid' ? 'success' : 'warning' }}">
                                            {{ $order->payment_status == 'paid' ? 'Đã thanh toán' : 'Chưa thanh toán' }}
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="order-info-card">
                                <div class="order-info-header">
                                    <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Thông tin đơn hàng</h6>
                                </div>
                                                                 <div class="order-info-body">
                                     <p><strong>Mã đơn hàng:</strong> #{{ $order->id }}</p>
                                     <p><strong>Trạng thái đơn hàng:</strong> 
                                         <span class="status-badge status-{{ $order->status }}">
                                             {{ getOrderStatusText($order->status) }}
                                         </span>
                                     </p>
                                     <p><strong>Ngày tạo:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</p>
                                     <p><strong>Cập nhật lần cuối:</strong> {{ $order->updated_at->format('d/m/Y H:i') }}</p>
                                 </div>
                            </div>
                        </div>
                    </div>

                    <!-- Products Table -->
                    <div class="product-table">
                        @if($order->orderItems && $order->orderItems->count() > 0)
                            <table class="table table-hover mb-0">
                                <thead>
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
                                        @if($item->variant && $item->variant->product)
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
                                                    @if($item->variant->product->thumbnail_url)
                                                        <img src="{{ $item->variant->product->thumbnail_url }}" 
                                                             alt="{{ $item->variant->product->name }}" 
                                                             class="product-image">
                                                    @else
                                                        <img src="{{ asset('images/default-product.jpg') }}" 
                                                             alt="Hình ảnh mặc định" 
                                                             class="product-image">
                                                    @endif
                                                </td>
                                                <td>
                                                    <strong>{{ $item->variant->product->name }}</strong>
                                                    @if($item->variant)
                                                        <br><small class="text-muted">Phân loại: {{ $item->variant->name }}</small>
                                                    @endif
                                                </td>
                                                <td class="text-center">{{ $item->variant ? $item->variant->name : 'N/A' }}</td>
                                                <td class="text-center">{{ $item->quantity }}</td>
                                                <td class="text-end">
                                                    @if($originalPrice > $orderPrice)
                                                        <div class="price-original">{{ number_format($originalPrice, 0, ',', '.') }}đ</div>
                                                    @else
                                                        {{ number_format($originalPrice, 0, ',', '.') }}đ
                                                    @endif
                                                </td>
                                                <td class="text-end">
                                                    <div class="price-final">{{ number_format($orderPrice, 0, ',', '.') }}đ</div>
                                                    @if($originalPrice > $orderPrice)
                                                        <div class="price-discount">Giảm {{ number_format($originalPrice - $orderPrice, 0, ',', '.') }}đ</div>
                                                    @endif
                                                </td>
                                                <td class="text-end">
                                                    <strong class="price-final">{{ number_format($itemTotal, 0, ',', '.') }}đ</strong>
                                                </td>
                                            </tr>
                                        @else
                                            <tr>
                                                <td colspan="7" class="text-center text-danger">
                                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                                    Sản phẩm này không còn tồn tại hoặc đã bị lỗi dữ liệu.
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>

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
                                                     <span class="text-success text-end">Miễn phí</span>
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
                        @else
                            <div class="text-center p-5">
                                <i class="fas fa-exclamation-triangle text-warning mb-3" style="font-size: 3rem;"></i>
                                <h5>Không có sản phẩm nào trong đơn hàng này.</h5>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

                         <!-- Action Buttons -->
             <div class="mt-4">
                 <a href="{{ route('client.orders.index') }}" class="btn btn-secondary me-2">
                     <i class="fas fa-arrow-left me-2"></i>Quay lại danh sách
                 </a>
                 @if($order->status == 'pending')
                     <button type="button" class="btn btn-danger" onclick="confirmCancelOrder({{ $order->id }})">
                         <i class="fas fa-times me-2"></i>Hủy đơn hàng
                     </button>
                 @endif
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