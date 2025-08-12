@extends('layouts.client')
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

        .status-completed {
            background-color: #d4edda;
            color: #155724;
        }

        .status-delivered {
            background-color: #cce5ff;
            color: #004085;
        }

        .status-cancelled {
            background-color: #f8d7da;
            color: #721c24;
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
                                        <span class="info-value">{{ ucfirst($order->payment_status) }}</span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">Tổng tiền:</span>
                                        <span
                                            class="info-value text-success font-weight-bold">{{ number_format($order->total_price) }}
                                            VND</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Danh sách sản phẩm -->
                        <div class="info-section">
                            <h5><i class="fas fa-box"></i> Sản Phẩm Đã Đặt</h5>
                            @foreach ($order->orderItems as $item)
                                @if ($item->productVariant && $item->productVariant->product)
                                    <div class="product-item">
                                        <img src="{{ optional($item->productVariant->product)->thumbnail_url }}"
                                            class="product-image" alt="{{ $item->productVariant->product->name }}">
                                        <div class="product-info">
                                            <div class="product-name">{{ $item->productVariant->product->name }}</div>
                                            <div class="product-variant">Phân loại: {{ $item->productVariant->name }}</div>
                                            <div class="product-variant">Số lượng: {{ $item->quantity }}</div>
                                        </div>
                                        <div class="product-price">
                                            <span>Giá sản phẩm :</span>
                                            <span>{{ number_format($item->price, 0, ',', '.') }}VND</span>
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
@endsection
