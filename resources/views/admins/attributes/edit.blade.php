@extends('layouts.admin')
@section('title', 'Chỉnh sửa Thuộc tính')

@section('content')
<div class="container-fluid mt-4">
    <div class="card">
        <div class="card-header">
            <h4>Chỉnh sửa Thuộc tính: {{ $attribute->name }}</h4>
        </div>
        <div class="card-body">
             @if($errors->any())
                <div class="alert alert-danger">
                    <ul>@foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach</ul>
                </div>
            @endif

            <form action="{{ route('admins.attributes.update', $attribute) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label for="name" class="form-label">Tên Thuộc tính</label>
                    <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $attribute->name) }}" required>
                </div>
                <div class="mb-3">
                    <label for="values" class="form-label">Các Giá trị</label>
                    {{-- Ghép các giá trị cũ thành một chuỗi để hiển thị --}}
                    <textarea name="values" id="values" class="form-control" rows="4" required>{{ old('values', $attribute->values->pluck('value')->implode(', ')) }}</textarea>
                    <div class="form-text">Nhập các giá trị, cách nhau bởi dấu phẩy.</div>
                </div>
                <button type="submit" class="btn btn-primary">Cập nhật</button>
                <a href="{{ route('admins.attributes.index') }}" class="btn btn-secondary">Hủy</a>
            </form>
        </div>
    </div>
</div>
@endsection