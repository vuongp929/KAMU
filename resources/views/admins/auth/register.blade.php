@extends('layouts.admins')

@section('title')
    Đăng Ký
@endsection

@section('content')
@if(session('alert'))
    <script type="text/javascript">
        alert("{{ session('alert') }}");
    </script>
@endif

<div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
    data-sidebar-position="fixed" data-header-position="fixed">
    <div class="position-relative overflow-hidden radial-gradient min-vh-100 d-flex align-items-center justify-content-center">
        <div class="d-flex align-items-center justify-content-center w-100">
            <div class="row justify-content-center w-100">
                <div class="col-md-8 col-lg-6 col-xxl-3">
                    <div class="card mb-0">
                        <div class="card-body">
                            <a href="#" class="text-nowrap logo-img text-center d-block py-3 w-100">
                                <img src="{{ asset('assets/auth/images/logos/logo.png') }}" width="180" alt="Logo">
                            </a>
                            <p class="text-center">Tạo tài khoản mới</p>
                            <form method="POST" action="{{ route('register') }}">
                                @csrf
                                <div class="mb-3">
                                    <label for="name" class="form-label">Họ và tên</label>
                                    <input type="text" name="name" class="form-control" id="name" placeholder="Nhập họ và tên" value="{{ old('name') }}">
                                    @error('name')
                                    <p class="text-danger fs-12 m-0">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" name="email" class="form-control" id="email" placeholder="Nhập email" value="{{ old('email') }}">
                                    @error('email')
                                    <p class="text-danger fs-12 m-0">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="mb-4">
                                    <label for="password" class="form-label">Mật khẩu</label>
                                    <input type="password" name="password" class="form-control" id="password" placeholder="Nhập mật khẩu">
                                    @error('password')
                                    <p class="text-danger fs-12 m-0">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="mb-4">
                                    <label for="password_confirmation" class="form-label">Xác nhận mật khẩu</label>
                                    <input type="password" name="password_confirmation" class="form-control" id="password_confirmation" placeholder="Nhập lại mật khẩu">
                                </div>
                                <button type="submit" class="btn btn-primary w-100 py-8 fs-4 mb-4 rounded-2">Đăng ký</button>
                                <div class="d-flex align-items-center justify-content-center">
                                    <p class="fs-4 mb-0 fw-bold">Bạn đã có tài khoản?</p>
                                    <a class="text-primary fw-bold ms-2" href="{{ route('login') }}">Đăng nhập</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
  
@endsection
