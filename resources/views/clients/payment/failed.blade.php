@extends('layouts.client')
@section('title', 'Thanh toán thất bại')
@section('content')
<div class="payment-result-page">
    <div class="result-card">
        <div class="result-icon" style="color: #dc3545;"> {{-- Màu đỏ thất bại --}}
            <i class="fas fa-times-circle"></i>
        </div>
        <h2>Thanh toán thất bại!</h2>
        
        @if(session('error_message'))
            <div class="alert alert-danger mt-3">
                <strong>Chi tiết lỗi:</strong><br>
                {{ session('error_message') }}
            </div>
            
            @if(str_contains(session('error_message'), 'thẻ/tài khoản bị khóa'))
                <div class="alert alert-info mt-3">
                    <strong>Hướng dẫn:</strong><br>
                    Bạn đang sử dụng thẻ test bị khóa. Vui lòng thử với các thẻ test khác:
                    <ul class="mt-2 mb-0">
                        <li><strong>Thẻ thành công:</strong> 9704 0000 0000 0018</li>
                        <li><strong>Thẻ không đủ tiền:</strong> 9704 0000 0000 0034</li>
                        <li><strong>Thẻ vượt hạn mức:</strong> 9704 0000 0000 0042</li>
                    </ul>
                </div>
            @endif
        @else
            <p>Đã có lỗi xảy ra trong quá trình thanh toán.</p>
        @endif

        <div class="mt-4">
            <a href="{{ route('client.checkout.index') }}" class="btn btn-primary me-2">Thử lại</a>
            <a href="{{ route('home') }}" class="btn btn-outline-secondary">Về trang chủ</a>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .payment-result-page {
        min-height: 80vh;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        padding: 2rem 1rem;
    }
    
    .result-card {
        background: white;
        border-radius: 20px;
        padding: 3rem 2rem;
        text-align: center;
        box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        max-width: 600px;
        width: 100%;
        animation: slideUp 0.6s ease-out;
    }
    
    .result-icon {
        font-size: 4rem;
        margin-bottom: 1.5rem;
        animation: pulse 2s infinite;
    }
    
    .result-card h2 {
        color: #dc3545;
        margin-bottom: 1rem;
        font-weight: 600;
    }
    
    .alert {
        text-align: left;
        border-radius: 10px;
    }
    
    .alert ul {
        padding-left: 1.2rem;
    }
    
    .btn {
        border-radius: 25px;
        padding: 0.75rem 2rem;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    
    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    }
    
    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    @keyframes pulse {
        0%, 100% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.1);
        }
    }
</style>
@endpush