@extends('layouts.admin')

@section('title', 'Danh sách Sản phẩm')

@section('content')
<div class="container-fluid mt-4">
    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Danh sách Sản phẩm</h4>
                <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
                    <i class="ri-add-line"></i> Thêm Sản phẩm
                </a>
            </div>
        </div>

        <div class="card-body">
            {{-- Thông báo thành công --}}
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-hover table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" style="width: 5%;">ID</th>
                            <th style="width: 10%;">Ảnh</th>
                            <th>Tên sản phẩm & Danh mục</th>
                            <th class="text-end" style="width: 15%;">Giá</th>
                            <th class="text-center" style="width: 10%;">Tổng tồn kho</th>
                            <th class="text-center" style="width: 15%;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                        <tr>
                            <td class="text-center">{{ $product->id }}</td>
                            <td>
                                {{-- SỬA LẠI: Dùng accessor 'thumbnail_url' đã tạo trong Model --}}
                                <img src="{{ $product->thumbnail_url }}" alt="{{ $product->name }}" class="img-thumbnail" style="width: 60px; height: 60px; object-fit: cover;">
                            </td>
                            <td>
                                <strong>{{ $product->name }}</strong>
                                <br>
                                {{-- Thêm: Hiển thị danh mục đã được tải trước --}}
                                @if($product->categories->isNotEmpty())
                                    <small class="text-muted">
                                        @foreach($product->categories as $category)
                                            <span class="badge bg-light text-dark">{{ $category->name }}</span>
                                        @endforeach
                                    </small>
                                @endif
                            </td>
                            <td class="text-end">
                                {{-- SỬA LẠI: Dùng accessor 'price_range' đã tạo trong Model --}}
                                {{ $product->price_range }}
                            </td>
                            <td class="text-center">
                                {{-- SỬA LẠI: Tính tổng tồn kho từ các biến thể hoặc lấy tồn kho cơ bản --}}
                                {{ $product->total_stock }}
                            </td>
                            <td class="text-center">
                                <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-sm btn-warning">
                                    <i class="ri-pencil-line"></i> Sửa
                                </a>
                                <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này không?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="ri-delete-bin-line"></i> Xoá
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">Chưa có sản phẩm nào.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Phân trang --}}
        @if ($products->hasPages())
        <div class="card-footer bg-white">
            {{ $products->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>
</div>
@endsection