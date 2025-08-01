@extends('clients.account.layout')

@section('account_content')
<div class="discount-codes-container">
    <div class="row">
        <div class="col-12">
            <h2 class="mb-4">
                <i class="fas fa-ticket-alt text-primary"></i>
                Mã Đổi Thưởng Của Tôi
            </h2>
        </div>
    </div>

    <!-- Thống kê tổng quan -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="stat-item">
                        <h4 class="text-primary mb-2">{{ $discountCodes->total() }}</h4>
                        <p class="text-muted mb-0">Tổng mã đã quy đổi</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="stat-item">
                        <h4 class="text-success mb-2">{{ $user->activeDiscountCodes()->count() }}</h4>
                        <p class="text-muted mb-0">Mã còn hiệu lực</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="stat-item">
                        <h4 class="text-warning mb-2">{{ $discountCodes->where('is_used', true)->count() }}</h4>
                        <p class="text-muted mb-0">Mã đã sử dụng</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Danh sách mã giảm giá -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-list"></i>
                        Danh Sách Mã Đổi Thưởng
                    </h5>
                </div>
                <div class="card-body">
                    @if($discountCodes->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Mã Giảm Giá</th>
                                        <th>Phần Trăm Giảm</th>
                                        <th>Điểm Đã Sử Dụng</th>
                                        <th>Ngày Tạo</th>
                                        <th>Hết Hạn</th>
                                        <th>Trạng Thái</th>
                                        <th>Thao Tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($discountCodes as $code)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <span class="badge badge-primary badge-pill mr-2">
                                                        <i class="fas fa-tag"></i>
                                                    </span>
                                                    <strong>{{ $code->discount_code }}</strong>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="text-success font-weight-bold">
                                                    {{ number_format($code->discount_percentage * 100, 1) }}%
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge badge-info badge-pill">
                                                    {{ number_format($code->points_used) }} điểm
                                                </span>
                                            </td>
                                            <td>
                                                {{ $code->created_at->format('d/m/Y H:i') }}
                                            </td>
                                            <td>
                                                {{ $code->expires_at->format('d/m/Y H:i') }}
                                            </td>
                                            <td>
                                                <span class="badge badge-{{ $code->status_color }}">
                                                    {{ $code->status_text }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($code->canBeUsed())
                                                    <button class="btn btn-sm btn-outline-primary copy-code" 
                                                            data-code="{{ $code->discount_code }}"
                                                            data-toggle="tooltip" 
                                                            title="Sao chép mã">
                                                        <i class="fas fa-copy"></i> Sao chép
                                                    </button>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Phân trang -->
                        <div class="d-flex justify-content-center mt-4">
                            {{ $discountCodes->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-ticket-alt fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Chưa có mã đổi thưởng nào</h5>
                            <p class="text-muted">Hãy quy đổi điểm thưởng để nhận mã giảm giá!</p>
                            <a href="{{ route('client.rewards.index') }}" class="btn btn-primary">
                                <i class="fas fa-exchange-alt"></i>
                                Quy Đổi Điểm Ngay
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Hướng dẫn sử dụng -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle"></i>
                        Hướng Dẫn Sử Dụng
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-primary">Cách sử dụng mã:</h6>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-check text-success"></i> Sao chép mã giảm giá</li>
                                <li><i class="fas fa-check text-success"></i> Áp dụng khi thanh toán đơn hàng</li>
                                <li><i class="fas fa-check text-success"></i> Mỗi mã chỉ sử dụng được 1 lần</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-primary">Lưu ý:</h6>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-info text-info"></i> Mã có hiệu lực trong 3 tháng</li>
                                <li><i class="fas fa-info text-info"></i> Áp dụng cho đơn hàng từ 100,000 VND</li>
                                <li><i class="fas fa-info text-info"></i> Mã đã sử dụng không thể dùng lại</li>
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
.discount-codes-container {
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

.copy-code {
    transition: all 0.2s;
}

.copy-code:hover {
    transform: scale(1.05);
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Khởi tạo tooltip
    $('[data-toggle="tooltip"]').tooltip();

    // Xử lý sao chép mã
    $('.copy-code').click(function() {
        const code = $(this).data('code');
        const button = $(this);
        
        // Sao chép vào clipboard
        navigator.clipboard.writeText(code).then(function() {
            // Thay đổi text tạm thời
            const originalText = button.html();
            button.html('<i class="fas fa-check"></i> Đã sao chép');
            button.removeClass('btn-outline-primary').addClass('btn-success');
            
            // Khôi phục sau 2 giây
            setTimeout(function() {
                button.html(originalText);
                button.removeClass('btn-success').addClass('btn-outline-primary');
            }, 2000);
        }).catch(function() {
            // Fallback cho trình duyệt cũ
            const textArea = document.createElement('textarea');
            textArea.value = code;
            document.body.appendChild(textArea);
            textArea.select();
            document.execCommand('copy');
            document.body.removeChild(textArea);
            
            // Thông báo
            alert('Đã sao chép mã: ' + code);
        });
    });
});
</script>
@endpush 