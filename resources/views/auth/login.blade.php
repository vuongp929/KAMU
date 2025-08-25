@extends('layouts.client') {{-- Kế thừa từ layout client chính của bạn --}}

@section('title', 'Đăng nhập')

@section('content')
<div class="auth-page-wrapper">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6 col-xl-5">
                <div class="auth-card">
                    <div class="auth-card-header">
                        <a href="{{ route('home') }}" class="auth-logo">KUMA House</a>
                        <p class="auth-subtitle">Chào mừng bạn trở lại!</p>
                    </div>

                    <div class="auth-card-body">
                        {{-- Hiển thị thông báo (ví dụ: sau khi đăng ký thành công) --}}
                        @if (session('status'))
                            <div class="alert alert-success mb-4">
                                {{ session('status') }}
                            </div>
                        @endif
                        
                        {{-- Hiển thị lỗi validation --}}
                        @if ($errors->any())
                             <div class="alert alert-danger mb-4">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('login') }}">
                            @csrf

                            <!-- Email -->
                            <div class="mb-3">
                                <label for="email" class="form-label">Địa chỉ Email</label>
                                <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required autofocus>
                            </div>

                            <!-- Password -->
                            <div class="mb-3">
                                <div class="d-flex justify-content-between">
                                    <label for="password" class="form-label">Mật khẩu</label>
                                    @if (Route::has('password.request'))
                                        <a href="{{ route('password.request') }}" class="forgot-password-link">Quên mật khẩu?</a>
                                    @endif
                                </div>
                                <input id="password" type="password" class="form-control" name="password" required>
                            </div>

                            <!-- Remember Me -->
                            <div class="mb-3 form-check">
                                <input id="remember_me" type="checkbox" class="form-check-input" name="remember">
                                <label for="remember_me" class="form-check-label">Ghi nhớ đăng nhập</label>
                            </div>

                            <!-- Nút đăng nhập -->
                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary w-100">Đăng nhập</button>
                            </div>

                            <div class="mt-4 text-center">
                                <p class="text-muted mb-0">Chưa có tài khoản? <a href="{{ route('register') }}" class="register-link">Tạo tài khoản ngay</a></p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


@push('styles')
{{-- CSS tùy chỉnh cho trang đăng nhập --}}
<style>
    .auth-page-wrapper {
        background-color: #f5f5f5; /* Màu nền xám nhạt */
        padding: 60px 0;
        min-height: 80vh;
        display: flex;
        align-items: center;
    }
    .auth-card {
        background-color: #fff;
        border: none;
        border-radius: 15px;
        box-shadow: 0 5px 25px rgba(0,0,0,0.08);
        overflow: hidden;
    }
    .auth-card-header {
        text-align: center;
        padding: 30px;
        background-color: #fffafc; /* Màu hồng rất nhạt */
        border-bottom: 1px solid #fde2f3;
    }
    .auth-logo {
        font-family: 'Pacifico', cursive;
        font-size: 2.5rem;
        color: #ea73ac; /* Màu hồng chủ đạo */
        text-decoration: none;
    }
    .auth-subtitle {
        margin-top: 10px;
        color: #5d3b80; /* Màu tím */
        font-size: 1.1rem;
    }
    .auth-card-body {
        padding: 30px;
    }
    .form-control {
        border-radius: 8px;
        padding: 12px 15px;
    }
    .form-control:focus {
        border-color: #ea73ac;
        box-shadow: 0 0 0 0.25rem rgba(234, 115, 172, 0.25);
    }
    .btn-primary {
        background-color: #ea73ac;
        border-color: #ea73ac;
        padding: 12px;
        border-radius: 8px;
        font-weight: 600;
    }
    .btn-primary:hover {
        background-color: #d66095;
        border-color: #d66095;
    }
    .forgot-password-link, .register-link {
        color: #ea73ac;
        text-decoration: none;
        font-weight: 600;
    }
    .forgot-password-link:hover, .register-link:hover {
        text-decoration: underline;
    }
</style>
@endpush