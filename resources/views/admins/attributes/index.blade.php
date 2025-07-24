@extends('layouts.admin')
@section('title', 'Quản lý Thuộc tính')

@section('content')
<div class="container-fluid mt-4">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Danh sách Thuộc tính</h4>
            <a href="{{ route('admins.attributes.create') }}" class="btn btn-primary">Thêm Thuộc tính</a>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>Tên Thuộc tính</th>
                        <th>Các Giá trị</th>
                        <th style="width: 15%;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($attributes as $attribute)
                        <tr>
                            <td><strong>{{ $attribute->name }}</strong></td>
                            <td>
                                @foreach($attribute->values as $value)
                                    <span class="badge bg-secondary">{{ $value->value }}</span>
                                @endforeach
                            </td>
                            <td>
                                <a href="{{ route('admins.attributes.edit', $attribute) }}" class="btn btn-sm btn-warning">Sửa</a>
                                <form action="{{ route('admins.attributes.destroy', $attribute) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Xoá</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center">Chưa có thuộc tính nào.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection