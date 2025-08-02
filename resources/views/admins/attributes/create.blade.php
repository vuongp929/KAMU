@extends('layouts.admin')
@section('title', 'Thêm Thuộc tính mới')

@section('content')
<div class="container-fluid mt-4">
    <div class="card">
        <div class="card-header">
            <h4>Thêm Thuộc tính mới</h4>
        </div>
        <div class="card-body">
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul>@foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach</ul>
                </div>
            @endif

            <form action="{{ route('admin.attributes.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="name" class="form-label">Tên Thuộc tính (ví dụ: Màu sắc, Kích cỡ)</label>
                    <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required>
                </div>
                <div class="mb-3">
                    <label for="values" class="form-label">Các Giá trị</label>
                    <textarea name="values" id="values" class="form-control" rows="4" required>{{ old('values') }}</textarea>
                    <div class="form-text">Nhập các giá trị, cách nhau bởi dấu phẩy. Ví dụ: Đỏ, Xanh, Vàng</div>
                </div>
                <button type="submit" class="btn btn-primary">Lưu Thuộc tính</button>
                <a href="{{ route('admin.attributes.index') }}" class="btn btn-secondary">Hủy</a>
            </form>
        </div>
    </div>
</div>
@endsection