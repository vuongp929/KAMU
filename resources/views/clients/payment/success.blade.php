@extends('layouts.client') {{-- Kế thừa từ layout client chính của bạn --}}

@section('title', 'Thanh toán thành công')

@push('styles')
<style>
    .payment-result-page {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 60vh;
        text-align: center;
    }
    .result-card {
        background-color: #fff;
        padding: 50px;
        border-radius: 15px;
        box-shadow: 0 5px 25px rgba(0,0,0,0.1);
        max-width: 500px;
    }
    .result-icon {
        color: #28a745; /* Màu xanh lá cây thành công */
        font-size: 80px;
        margin-bottom: 20px;
    }
    .result-card h2 {
        color: #333;
        font-weight: 600;
        margin-bottom: 15px;
    }
    .result-card p {
        color: #666;
        font-size: 1.1rem;
    }
    .btn-primary {
        background-color: #ea73ac;
        border-color: #ea73ac;
        font-weight: 600;
        padding: 10px 30px;
        margin-top: 20px;
    }
    .btn-primary:hover {
        background-color: #d66095;
        border-color: #d66095;
    }
</style>
@endpush

@section('content')
<div class="payment-result-page">
    <div class="result-card">
        <div class="result-icon">
            <i class="fas fa-check-circle"></i>
        </div>
        <h2>Thanh toán thành công!</h2>
        
        @if(session('success_message'))
            <p>{{ session('success_message') }}</p>
        @else
            <p>Cảm ơn bạn đã mua sắm. Đơn hàng của bạn đang được xử lý.</p>
        @endif

        <a href="{{ route('client.orders.index') }}" class="btn btn-primary">Xem lịch sử đơn hàng</a>
        <a href="{{ route('home') }}" class="btn btn-outline-secondary mt-3">Tiếp tục mua sắm</a>
    </div>
</div>
@endsection