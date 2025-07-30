@extends('layouts.client')

@section('content')
<div class="container my-5">
    <div class="row">
        {{-- Bắt đầu cột Sidebar bên trái --}}
        <div class="col-lg-3">
            <div class="profile-sidebar">
                <div class="user-info">
                    <div class="user-avatar"><i class="fas fa-user"></i></div>
                    <div>
                        <div class="user-name">{{ Auth::user()->name }}</div>
                        <div class="user-edit">
                            <a href="{{ route('profile.edit') }}"><i class="fas fa-pencil-alt"></i> Sửa Hồ Sơ</a>
                        </div>
                    </div>
                </div>
                <ul class="nav flex-column sidebar-nav">
                    <li class="nav-item">
                        <a class="nav-link {{ Route::is('profile.edit') ? 'active' : '' }}" href="{{ route('profile.edit') }}">
                            <i class="fas fa-user-circle"></i> Hồ Sơ
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ Route::is('client.orders.index') ? 'active' : '' }}" href="{{ route('client.orders.index') }}">
                            <i class="fas fa-clipboard-list"></i> Đơn Mua
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ Route::is('client.rewards.index') ? 'active' : '' }}" href="{{ route('client.rewards.index') }}">
                            <i class="fas fa-star"></i> Điểm Thưởng
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ Route::is('client.rewards.discount-codes') ? 'active' : '' }}" href="{{ route('client.rewards.discount-codes') }}">
                            <i class="fas fa-ticket-alt"></i> Mã Đổi Thưởng
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="fas fa-bell"></i> Thông Báo</a>
                    </li>
                </ul>
            </div>
        </div>
        {{-- Kết thúc cột Sidebar --}}

        {{-- Bắt đầu cột Nội dung chính bên phải --}}
        <div class="col-lg-9">
            {{-- THÊM LỚP .profile-content Ở ĐÂY --}}
            <div class="profile-content">
                {{-- Nội dung của từng trang sẽ được chèn vào đây --}}
                @yield('account_content')
            </div>
        </div>
        {{-- Kết thúc cột Nội dung chính --}}
    </div>
</div>
@endsection

@push('styles')
{{-- CSS chung cho toàn bộ khu vực tài khoản --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
<style>
    body { background-color: #f5f5f5; }
    .main { padding-top: 20px; padding-bottom: 20px; }
    
    /* Sidebar CSS */
    .profile-sidebar { background-color: #fff; padding: 20px; border-radius: 5px; }
    .user-info { display: flex; align-items: center; padding-bottom: 15px; border-bottom: 1px solid #f0f0f0; margin-bottom: 15px; }
    .user-avatar { width: 50px; height: 50px; border-radius: 50%; background-color: #e9ecef; margin-right: 15px; display: flex; align-items: center; justify-content: center; font-size: 24px; color: #adb5bd; }
    .user-name { font-weight: 600; color: #333; }
    .user-edit a { font-size: 14px; color: #888; text-decoration: none; transition: color 0.2s; }
    .user-edit a:hover { color: #0d6efd; }
    .sidebar-nav .nav-link { color: #333; padding: 12px 15px; font-size: 15px; display: flex; align-items: center; border-radius: 5px; transition: color 0.2s, background-color 0.2s; }
    .sidebar-nav .nav-link i { margin-right: 12px; width: 20px; text-align: center; }
    .sidebar-nav .nav-link.active { color: #0d6efd; font-weight: 600; background-color: #e7f1ff; }
    .sidebar-nav .nav-link:not(.active):hover { background-color: #f8f9fa; }

    /* Nội dung chính: đây là cái khung trắng bên ngoài */
    .profile-content { 
        background-color: #fff; 
        padding: 30px; /* Thêm padding ở đây để tạo khoảng đệm đồng nhất */
        border-radius: 5px;
    }
</style>
@endpush