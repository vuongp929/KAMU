@extends('layouts.admin')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">
                            <i class="fas fa-users me-2"></i>Danh sách người dùng
                        </h4>
                        <div class="d-flex gap-2">
                            <span class="badge bg-info">Tổng: {{ $users->total() }} người dùng</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped">
                                <thead class="table-dark">
                                    <tr>
                                        <th><i class="fas fa-user me-1"></i>Tên</th>
                                        <th><i class="fas fa-envelope me-1"></i>Email</th>
                                        <th><i class="fas fa-shield-alt me-1"></i>Quyền</th>
                                        <th><i class="fas fa-toggle-on me-1"></i>Trạng thái</th>
                                        <th><i class="fas fa-cogs me-1"></i>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($users as $item)
                                        <tr>
                                            <td>{{ $item->name }}</td>
                                            <td>{{ $item->email }}</td>
                                            <td>
                                @if ($item->roles->isNotEmpty())
                                    @php
                                        $userRole = $item->roles->first()->role;
                                    @endphp
                                    @if ($userRole == 'admin')
                                        <span class="badge bg-danger"><i class="fas fa-crown me-1"></i>Admin</span>
                                    @elseif ($userRole == 'customer')
                                        <span class="badge bg-primary"><i class="fas fa-user me-1"></i>Customer</span>
                                    @else
                                        <span class="badge bg-secondary"><i class="fas fa-question me-1"></i>{{ ucfirst($userRole) }}</span>
                                    @endif
                                @else
                                    <span class="badge bg-warning text-dark"><i class="fas fa-exclamation-triangle me-1"></i>Chưa phân quyền</span>
                                @endif
                            </td>
                                            <td>
                                                @if($item->status == 'active')
                                                    <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>Hoạt động</span>
                                                @else
                                                    <span class="badge bg-danger"><i class="fas fa-lock me-1"></i>Đã khóa</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.users.edit', $item->id) }}"
                                                    class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-edit me-1"></i>Chỉnh sửa
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-4">
                                                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                                <p class="text-muted">Không có người dùng nào được tìm thấy</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                            
                            @if($users->hasPages())
                                <div class="d-flex justify-content-center mt-3">
                                    {{ $users->links() }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @if (session('success'))
        <div class="alert alert-success animate__animated animate__slideInRight"
            style="position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
            {{ session('success') }}
        </div>
        <script>
            setTimeout(function() {
                const alert = document.querySelector('.alert-success');
                if (alert) {
                    alert.classList.remove('animate__slideInRight');
                    alert.classList.add('animate__slideOutUp');
                    setTimeout(() => alert.remove(), 1000);
                }
            }, 3000);
        </script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    @endif
@endsection

@section('styles')
    <link rel="stylesheet" href="{{ asset('admins/css/bootstrap1.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('admins/css/style1.css') }}" />
    <link rel="stylesheet" href="{{ asset('admins/css/colors/default.css') }}" id="colorSkinCSS">
@endsection
