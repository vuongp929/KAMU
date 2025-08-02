@extends('layouts.client')

@section('title', 'Đăng ký tài khoản')

@section('content')
<div class="auth-page-wrapper">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6 col-xl-5">
                <div class="auth-card">
                    <div class="auth-card-header">
                        <a href="{{ route('home') }}" class="auth-logo">Ôm Là Yêu</a>
                        <p class="auth-subtitle">Tạo tài khoản mới</p>
                    </div>

                    <div class="auth-card-body">
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

                        <form method="POST" action="{{ route('register') }}">
                            @csrf

                            <!-- Name -->
                            <div class="mb-3">
                                <label for="name" class="form-label">Tên của bạn</label>
                                <input id="name" type="text" class="form-control" name="name" value="{{ old('name') }}" required autofocus>
                            </div>

                            <!-- Email Address -->
                            <div class="mb-3">
                                <label for="email" class="form-label">Địa chỉ Email</label>
                                <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required>
                            </div>

                            <!-- Password -->
                            <div class="mb-3">
                                <label for="password" class="form-label">Mật khẩu</label>
                                <input id="password" type="password" class="form-control" name="password" required>
                            </div>
                            
                            <!-- Confirm Password -->
                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">Xác nhận mật khẩu</label>
                                <input id="password_confirmation" type="password" class="form-control" name="password_confirmation" required>
                            </div>


                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary w-100">Đăng ký</button>
                            </div>

                            <div class="mt-4 text-center">
                                <p class="text-muted mb-0">Đã có tài khoản? <a href="{{ route('login') }}" class="register-link">Đăng nhập ngay</a></p>
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
{{-- Tái sử dụng CSS từ trang đăng nhập --}}
<style>
    .auth-page-wrapper {
        background-color: #f5f5f5; padding: 60px 0; min-height: 80vh; display: flex; align-items: center;
    }
    .auth-card {
        background-color: #fff; border: none; border-radius: 15px; box-shadow: 0 5px 25px rgba(0,0,0,0.08); overflow: hidden;
    }
    .auth-card-header {
        text-align: center; padding: 30px; background-color: #fffafc; border-bottom: 1px solid #fde2f3;
    }
    .auth-logo {
        font-family: 'Pacifico', cursive; font-size: 2.5rem; color: #ea73ac; text-decoration: none;
    }
    .auth-subtitle {
        margin-top: 10px; color: #5d3b80; font-size: 1.1rem;
    }
    .auth-card-body { padding: 30px; }
    .form-control { border-radius: 8px; padding: 12px 15px; }
    .form-control:focus { border-color: #ea73ac; box-shadow: 0 0 0 0.25rem rgba(234, 115, 172, 0.25); }
    .btn-primary { background-color: #ea73ac; border-color: #ea73ac; padding: 12px; border-radius: 8px; font-weight: 600; }
    .btn-primary:hover { background-color: #d66095; border-color: #d66095; }
    .register-link { color: #ea73ac; text-decoration: none; font-weight: 600; }
    .register-link:hover { text-decoration: underline; }
</style>
@endpush