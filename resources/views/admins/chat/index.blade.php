@extends('layouts.admin')

@section('title', 'Hộp thư hỗ trợ')

@section('content')
<div class="container-fluid mt-4">
    {{-- Page Title --}}
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Hộp thư hỗ trợ</h4>
            </div>
        </div>
    </div>

    {{-- Chat App --}}
    <div class="card">
        <div class="card-body p-0">
            <div class="chat-container">
                {{-- Cột bên trái: Danh sách User --}}
                <div class="chat-sidebar">
                    <div class="sidebar-header">
                        <div class="input-group">
                            <input type="text" id="user-search-input" class="form-control" placeholder="Tìm khách hàng...">
                            <button class="btn btn-primary" type="button" id="user-search-btn">
                                <i class="ri-search-line"></i>
                            </button>
                        </div>
                    </div>
                    <div class="user-list" id="user-list-container">
                        {{-- Danh sách user ban đầu được render bởi Blade --}}
                        @forelse($users as $user)
                            <div class="user-item" data-user-id="{{ $user->id }}" data-user-name="{{ $user->name }}">
                                <div class="user-avatar">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                                <span class="user-name">{{ $user->name }}</span>
                            </div>
                        @empty
                            <p class="text-center text-muted p-3">Chưa có cuộc trò chuyện nào.</p>
                        @endforelse
                    </div>
                </div>

                {{-- Cột bên phải: Khung chat --}}
                <div class="chat-main">
                    <div id="no-chat-selected" class="d-flex align-items-center justify-content-center h-100">
                        <p class="text-muted">Chọn một khách hàng để bắt đầu trò chuyện</p>
                    </div>
                    <div id="chat-window" class="d-none" style="display: flex; flex-direction: column; height: 100%;">
                        <div class="chat-header-main" id="chat-header-main"></div>
                        <div class="chat-messages" id="chat-messages-container"></div>
                        <div class="chat-footer">
                            <input type="text" id="chat-message-input" class="form-control chat-input" placeholder="Nhập tin nhắn...">
                            <button class="btn btn-primary send-btn" id="send-message-btn"><i class="ri-send-plane-2-fill"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
{{-- CSS tùy chỉnh cho trang chat, phong cách Velzon --}}
<style>
    .chat-container { display: flex; height: calc(100vh - 200px); min-height: 500px; }
    .chat-sidebar { width: 320px; border-right: 1px solid #e9e9ef; display: flex; flex-direction: column; }
    .sidebar-header { padding: 1rem; border-bottom: 1px solid #e9e9ef; }
    #user-search-input { border-radius: 30px; }
    .user-list { flex-grow: 1; overflow-y: auto; }
    .user-item { display: flex; align-items: center; padding: 0.8rem 1rem; cursor: pointer; border-bottom: 1px solid #f0f0f0; }
    .user-item:hover { background-color: #f8f9fa; }
    .user-item.active { background-color: #e6eff8; }
    .user-avatar { width: 40px; height: 40px; border-radius: 50%; background-color: #4b38b3; color: white; display: flex; align-items: center; justify-content: center; margin-right: 12px; font-weight: 500; flex-shrink: 0; }
    .user-name { font-weight: 500; color: #495057; }
    .chat-main { flex-grow: 1; display: flex; flex-direction: column; }
    .chat-header-main { padding: 1rem; border-bottom: 1px solid #e9e9ef; font-weight: 500; background-color: #f8f9fa; }
    .chat-messages { flex-grow: 1; padding: 1.5rem; overflow-y: auto; display: flex; flex-direction: column; gap: 1rem; }
    .message-item { max-width: 75%; padding: 0.75rem 1rem; border-radius: 1.25rem; word-wrap: break-word; line-height: 1.4; }
    .message-item.received { background-color: #f0f2f5; align-self: flex-start; border-bottom-left-radius: 0.25rem; }
    .message-item.sent { background-color: #4b38b3; color: white; align-self: flex-end; border-bottom-right-radius: 0.25rem; }
    .chat-footer { padding: 1rem; border-top: 1px solid #e9e9ef; display: flex; align-items: center; background-color: #f8f9fa; }
    .chat-input { flex-grow: 1; border-radius: 30px; padding: 0.75rem 1.25rem; }
    .send-btn { border-radius: 50%; width: 45px; height: 45px; margin-left: 10px; flex-shrink: 0; }
</style>
@endpush

@push('scripts')
{{-- JavaScript cho trang chat admin --}}
    <script>
        window.adminId = {{ Auth::id() }};
    </script>
    <script src="{{ asset('js/admin-chat-custom.js') }}"></script>
    @vite(['resources/js/admin-chat.js'])

@endpush