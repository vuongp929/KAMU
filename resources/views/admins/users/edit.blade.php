@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    @if ($errors->any())
        <div id="error-alert" class="alert alert-danger animate__animated animate__slideInRight" style="position: relative; z-index: 9999; min-width: 300px;">
            Sửa user bị lỗi:
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
                            <h3 class="m-0">Sửa người dùng</h3>
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
                    <form action="{{ route('admin.users.update', ['id'=> $user->id]) }}" method="POST">
                        @csrf
                        @method('POST')
                        <!-- Hiển thị thông tin người dùng (chỉ đọc) -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="card border-info">
                                    <div class="card-header bg-info text-white">
                                        <h5 class="mb-0"><i class="fas fa-user"></i> Thông tin người dùng</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <p class="mb-2"><strong><i class="fas fa-user-circle"></i> Tên:</strong> <span class="text-primary">{{ $user->name }}</span></p>
                                                <p class="mb-2"><strong><i class="fas fa-envelope"></i> Email:</strong> <span class="text-primary">{{ $user->email }}</span></p>
                                            </div>
                                            <div class="col-md-6">
                                                <p class="mb-2"><strong><i class="fas fa-calendar-alt"></i> Ngày tạo:</strong> <span class="text-muted">{{ $user->created_at->format('d/m/Y H:i') }}</span></p>
                                                <p class="mb-0"><strong><i class="fas fa-clock"></i> Cập nhật cuối:</strong> <span class="text-muted">{{ $user->updated_at->format('d/m/Y H:i') }}</span></p>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="alert alert-warning mb-0">
                                            <i class="fas fa-info-circle"></i> <strong>Lưu ý:</strong> Chỉ có thể chỉnh sửa trạng thái và quyền của người dùng. Thông tin cá nhân không thể thay đổi từ trang này.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Form chỉnh sửa -->
                        <div class="card border-primary">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0"><i class="fas fa-edit"></i> Chỉnh sửa quyền và trạng thái</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="role" class="form-label fw-bold"><i class="fas fa-user-tag me-2"></i> Vai trò người dùng</label>
                                            <select class="form-select form-select-lg @error('role') is-invalid @enderror" id="role" name="role" style="font-size: 16px; padding: 12px 16px;">
                                                <option value="">-- Chọn vai trò --</option>
                                                @php
                                                    $currentRole = $user->roles->isNotEmpty() ? $user->roles->first()->role : '';
                                                @endphp
                                                <option value="admin" {{ old('role', $currentRole) == 'admin' ? 'selected' : '' }}>
                                                    👑 Quản trị viên - Toàn quyền hệ thống
                                                </option>
                                                <option value="customer" {{ old('role', $currentRole) == 'customer' ? 'selected' : '' }}>
                                                    👤 Khách hàng - Quyền cơ bản
                                                </option>
                                            </select>
                                            @error('role')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div class="form-text mt-2">
                                                <small class="text-muted">
                                                    <i class="fas fa-info-circle"></i> 
                                                    Vai trò quyết định quyền truy cập của người dùng trong hệ thống
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="status" class="form-label fw-bold"><i class="fas fa-toggle-on me-2"></i> Trạng thái tài khoản</label>
                                            <select class="form-select form-select-lg @error('status') is-invalid @enderror" id="status" name="status" style="font-size: 16px; padding: 12px 16px;">
                                                <option value="active" {{ old('status', $user->status) == 'active' ? 'selected' : '' }}>
                                                    ✅ Hoạt động - Cho phép đăng nhập
                                                </option>
                                                <option value="inactive" {{ old('status', $user->status) == 'inactive' ? 'selected' : '' }}>
                                                    🔒 Đã khóa - Ngăn chặn đăng nhập
                                                </option>
                                            </select>
                                            @error('status')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div class="form-text mt-2">
                                                <small class="text-muted">
                                                    <i class="fas fa-info-circle"></i> 
                                                    Tài khoản bị khóa sẽ không thể đăng nhập vào hệ thống
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <hr>
                                
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left"></i> Quay lại
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Cập nhật người dùng
                                    </button>
                                </div>
                            </div>
                        </div>
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
