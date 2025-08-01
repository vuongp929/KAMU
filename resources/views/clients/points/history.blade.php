@extends('clients.account.layout')

@section('account_content')
<div class="reward-history-container">
    <div class="row">
        <div class="col-12">
            <h2 class="mb-4">
                <i class="fas fa-history text-info"></i>
                Lịch Sử Điểm Thưởng
            </h2>
        </div>
    </div>

    <!-- Thống kê tổng quan -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="stat-item">
                        <h4 class="text-primary mb-2">{{ number_format($user->reward_points) }}</h4>
                        <p class="text-muted mb-0">Điểm hiện tại</p>
                    </div>
                </div>
            </div>
        </div>
                                <div class="col-md-4">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body text-center">
                                    <div class="stat-item">
                                        <h4 class="text-success mb-2">{{ $orders->total() }}</h4>
                                        <p class="text-muted mb-0">Đơn hàng đã thanh toán</p>
                                    </div>
                                </div>
                            </div>
                        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="stat-item">
                        <h4 class="text-warning mb-2">{{ number_format($orders->total() * 20) }}</h4>
                        <p class="text-muted mb-0">Tổng điểm đã tích</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Danh sách đơn hàng -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-list"></i>
                        Danh Sách Đơn Hàng Đã Thanh Toán
                    </h5>
                </div>
                <div class="card-body">
                    @if($orders->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Mã Đơn Hàng</th>
                                        <th>Ngày Đặt</th>
                                        <th>Tổng Tiền</th>
                                        <th>Điểm Nhận Được</th>
                                        <th>Trạng Thái</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($orders as $order)
                                        <tr>
                                            <td>
                                                <strong>#{{ $order->id }}</strong>
                                            </td>
                                            <td>
                                                {{ $order->created_at->format('d/m/Y H:i') }}
                                            </td>
                                            <td>
                                                <span class="text-success font-weight-bold">
                                                    {{ number_format($order->total_price) }} VND
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge badge-primary badge-pill">
                                                    +20 điểm
                                                </span>
                                            </td>
                                            <td>
                                                @if($order->payment_status == 'paid')
                                                    <span class="badge badge-success">Đã thanh toán</span>
                                                @else
                                                    <span class="badge badge-secondary">{{ ucfirst($order->payment_status) }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Phân trang -->
                        <div class="d-flex justify-content-center mt-4">
                            {{ $orders->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Chưa có đơn hàng nào đã thanh toán</h5>
                            <p class="text-muted">Hãy mua sắm và thanh toán để tích điểm thưởng!</p>
                            <a href="{{ route('home') }}" class="btn btn-primary">
                                <i class="fas fa-shopping-cart"></i>
                                Mua Sắm Ngay
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Thông tin bổ sung -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle"></i>
                        Thông Tin Điểm Thưởng
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-primary">Cách tích điểm:</h6>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-check text-success"></i> Mỗi đơn hàng thành công: +20 điểm</li>
                                <li><i class="fas fa-check text-success"></i> Điểm được cộng tự động khi đặt hàng</li>
                                <li><i class="fas fa-check text-success"></i> Không giới hạn số lượng đơn hàng</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-primary">Quy đổi điểm:</h6>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-exchange-alt text-info"></i> 100 điểm = 5% giảm giá</li>
                                <li><i class="fas fa-exchange-alt text-info"></i> 200 điểm = 6% giảm giá</li>
                                <li><i class="fas fa-exchange-alt text-info"></i> 400 điểm = 7% giảm giá</li>
                                <li><i class="fas fa-exchange-alt text-info"></i> 600 điểm = 8% giảm giá</li>
                                <li><i class="fas fa-exchange-alt text-info"></i> 800 điểm = 9% giảm giá</li>
                                <li><i class="fas fa-exchange-alt text-info"></i> 1000 điểm = 10% giảm giá</li>
                                <li><i class="fas fa-exchange-alt text-info"></i> Mã giảm giá có hiệu lực 3 tháng</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.reward-history-container {
    padding: 20px 0;
}

.stat-item h4 {
    font-size: 1.8rem;
    font-weight: bold;
}

.card {
    border-radius: 10px;
    transition: transform 0.2s;
}

.card:hover {
    transform: translateY(-2px);
}

.card-header {
    border-radius: 10px 10px 0 0 !important;
}

.table th {
    border-top: none;
    font-weight: 600;
    color: #495057;
}

.table td {
    vertical-align: middle;
}

.badge {
    font-size: 0.8rem;
}

.badge-pill {
    padding-left: 0.8rem;
    padding-right: 0.8rem;
}

.pagination {
    justify-content: center;
}

.page-link {
    color: #007bff;
    border-color: #dee2e6;
}

.page-item.active .page-link {
    background-color: #007bff;
    border-color: #007bff;
}

.list-unstyled li {
    margin-bottom: 8px;
}

.list-unstyled i {
    margin-right: 8px;
}
</style>
@endpush 