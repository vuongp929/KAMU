@extends('clients.account.layout')

@section('title', 'Hồ Sơ Của Tôi')

@section('account_content')

    {{-- Phần 1: Cập nhật thông tin hồ sơ --}}
    <div class="profile-section">
        @include('profile.partials.update-profile-information-form')
    </div>

    <hr class="my-4"> {{-- Thêm đường kẻ ngang ngăn cách --}}

    {{-- Phần 2: Cập nhật mật khẩu --}}
    <div class="profile-section">
        @include('profile.partials.update-password-form')
    </div>

    <hr class="my-4"> {{-- Thêm đường kẻ ngang ngăn cách --}}

    {{-- Phần 3: Xóa tài khoản --}}
    <div class="profile-section">
        @include('profile.partials.delete-user-form')
    </div>

@endsection

@push('styles')
{{-- Thêm một chút CSS để các phần trông đẹp hơn --}}
<style>
    .profile-section header h5.card-title {
        font-size: 1.2rem;
        color: #333;
        font-weight: 600;
    }
    .profile-section header p {
        font-size: 0.9rem;
    }
</style>
@endpush