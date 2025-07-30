@extends('clients.account.layout')

@section('account_content')
<div class="reward-points-container">
    <div class="row">
        <div class="col-12">
            <h2 class="mb-4">
                <i class="fas fa-star text-warning"></i>
                Điểm Thưởng
            </h2>
        </div>
    </div>

    <!-- Thông tin điểm thưởng hiện tại -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="reward-points-display">
                        <h3 class="text-primary mb-2">{{ number_format($user->reward_points) }}</h3>
                        <p class="text-muted mb-0">Điểm thưởng hiện tại</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                                            <div class="reward-info">
                            <h4 class="text-success mb-2">{{ $user->orders->where('payment_status', 'paid')->count() }}</h4>
                            <p class="text-muted mb-0">Đơn hàng đã thanh toán</p>
                        </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quy đổi điểm thưởng -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-exchange-alt"></i>
                        Quy Đổi Điểm Thưởng
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <p class="text-muted">
                                <strong>Quy tắc quy đổi:</strong><br>
                                • 100 điểm = 5% giảm giá<br>
                                • 200 điểm = 6% giảm giá<br>
                                • 400 điểm = 7% giảm giá<br>
                                • 600 điểm = 8% giảm giá<br>
                                • 800 điểm = 9% giảm giá<br>
                                • 1000 điểm = 10% giảm giá (tối đa)<br>
                                • Mã giảm giá có hiệu lực trong 3 tháng
                            </p>
                        </div>
                        <div class="col-md-4">
                            @if($user->reward_points >= 100)
                                <form action="{{ route('client.rewards.exchange') }}" method="POST">
                                    @csrf
                                    <div class="form-group">
                                        <label for="points">Số điểm muốn quy đổi:</label>
                                        <select class="form-control" id="points" name="points" required>
                                            <option value="">Chọn số điểm</option>
                                            @if($user->reward_points >= 100)
                                                <option value="100">100 điểm = 5% giảm giá</option>
                                            @endif
                                            @if($user->reward_points >= 200)
                                                <option value="200">200 điểm = 6% giảm giá</option>
                                            @endif
                                            @if($user->reward_points >= 400)
                                                <option value="400">400 điểm = 7% giảm giá</option>
                                            @endif
                                            @if($user->reward_points >= 600)
                                                <option value="600">600 điểm = 8% giảm giá</option>
                                            @endif
                                            @if($user->reward_points >= 800)
                                                <option value="800">800 điểm = 9% giảm giá</option>
                                            @endif
                                            @if($user->reward_points >= 1000)
                                                <option value="1000">1000 điểm = 10% giảm giá</option>
                                            @endif
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-block mt-3">
                                        <i class="fas fa-exchange-alt"></i>
                                        Quy Đổi Ngay
                                    </button>
                                </form>
                            @else
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    Bạn cần ít nhất 100 điểm để quy đổi.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Thông tin bổ sung -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle"></i>
                        Thông Tin Bổ Sung
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-primary">Cách tích điểm:</h6>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-check text-success"></i> Mỗi đơn hàng đã thanh toán: +20 điểm</li>
                                <li><i class="fas fa-check text-success"></i> Đơn hàng phải có trạng thái thanh toán "paid"</li>
                                <li><i class="fas fa-check text-success"></i> Điểm được cộng tự động khi thanh toán thành công</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-primary">Lưu ý:</h6>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-info text-info"></i> Điểm không có thời hạn</li>
                                <li><i class="fas fa-info text-info"></i> Mã giảm giá chỉ sử dụng được 1 lần</li>
                                <li><i class="fas fa-info text-info"></i> Áp dụng cho đơn hàng từ 100,000 VND</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle"></i>
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert">
            <span>&times;</span>
        </button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle"></i>
        {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert">
            <span>&times;</span>
        </button>
    </div>
@endif
@endsection

@push('styles')
<style>
.reward-points-container {
    padding: 20px 0;
}

.reward-points-display h3 {
    font-size: 2.5rem;
    font-weight: bold;
    color: #007bff;
}

.reward-info h4 {
    font-size: 2rem;
    font-weight: bold;
    color: #28a745;
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

.btn-block {
    width: 100%;
}

.alert {
    border-radius: 8px;
}

.list-unstyled li {
    margin-bottom: 8px;
}

.list-unstyled i {
    margin-right: 8px;
}
</style>
@endpush 