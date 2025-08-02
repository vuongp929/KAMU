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
            <p>{{ session('error_message') }}</p>
        @else
            <p>Đã có lỗi xảy ra trong quá trình thanh toán.</p>
        @endif

        <a href="{{ route('client.checkout.index') }}" class="btn btn-primary mt-3">Thử lại</a>
        <a href="{{ route('home') }}" class="btn btn-outline-secondary mt-3">Về trang chủ</a>
    </div>
</div>
@endsection

@push('styles')
    {{-- Bạn có thể copy lại khối <style> từ trang success --}}
@endpush