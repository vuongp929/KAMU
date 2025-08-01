@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    @if ($errors->any())
        <div id="error-alert" class="alert alert-danger animate__animated animate__slideInRight" style="position: relative; z-index: 9999; min-width: 300px;">
            Thêm user bị lỗi:
        </div>
        <script>
            setTimeout(function() {
                const alert = document.getElementById('error-alert');
                if(alert) {
                    alert.classList.remove('animate__slideInRight');
                    alert.classList.add('animate__slideOutUp');
                    setTimeout(() => alert.remove(), 1000);
                }
            }, 3000);
        </script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    @endif
    <div class="row">
        <div class="col-12">
            <div class="white_card card_height_100 mb_30">
                <div class="white_card_header">
                    <div class="box_header m-0">
                        <div class="main-title">
                            <h3 class="m-0">Thêm người dùng mới</h3>
                        </div>
                    </div>
                </div>
                <div class="white_card_body">
                    @if (session('success'))
                        <div class="alert alert-success animate__animated animate__slideInRight" style="position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
                            {{ session('success') }}
                        </div>
                        <script>
                            setTimeout(function() {
                                const alert = document.querySelector('.alert-success');
                                if(alert) {
                                    alert.classList.remove('animate__slideInRight');
                                    alert.classList.add('animate__slideOutUp');
                                    setTimeout(() => alert.remove(), 1000);
                                }
                            }, 3000);
                        </script>
                        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
                    @endif
                    <form action="{{ route('admins.users.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="name">Tên người dùng</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}">
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="email">Email</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="password">Mật khẩu</label>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password">
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="role">Vai trò</label>
                                    <select class="form-control @error('role') is-invalid @enderror" id="role" name="role">
                                        <option value="">-- Chọn vai trò --</option>
                                        <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Quản trị viên</option>
                                        <option value="customer" {{ old('role') == 'customer' ? 'selected' : '' }}>Người dùng</option>
                                    </select>
                                    @error('role')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Thêm người dùng</button>
                        <a href="{{ route('admins.users.index') }}" class="btn btn-secondary">Quay lại</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
    <link rel="stylesheet" href="{{ asset('admins/css/bootstrap1.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('admins/css/style1.css') }}" />
    <link rel="stylesheet" href="{{ asset('admins/css/colors/default.css') }}" id="colorSkinCSS">
@endsection
