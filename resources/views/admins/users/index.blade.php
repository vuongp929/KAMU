@extends('layouts.admin')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="white_card card_height_100 mb_30">
                    <div class="white_card_header">
                        <div class="box_header m-0">
                            <div class="main-title">
                                <h3 class="m-0">Danh sách người dùng</h3>
                            </div>
                            <div class="header_more_tool">
                                <a href="{{ route('admins.users.create') }}" class="btn btn-primary">Thêm người dùng</a>
                            </div>
                        </div>
                    </div>
                    <div class="white_card_body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Tên</th>
                                        <th>Email</th>
                                        <th>Quyền</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($users as $item)
                                        <tr>
                                            <td>{{ $item->name }}</td>
                                            <td>{{ $item->email }}</td>
                                            <td>
                                                @if ($item->role && $item->role->role == 'admin')
                                                    Admin
                                                @elseif ($item->role && $item->role->role == 'customer')
                                                    Customer
                                                @else
                                                    <span class="badge badge-secondary">N/A</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('admins.users.edit', $item->id) }}"
                                                    class="btn btn-sm btn-info">Sửa</a>
                                                <form action="{{ route('admins.users.destroy', $item->id) }}"
                                                    method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger"
                                                        onclick="return confirm('Bạn có chắc chắn muốn xóa?')">Xóa</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                    @endforelse
                                </tbody>
                            </table>
                            {{ $users->links() }}
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
