@extends('layouts.admin')

@section('title', 'Danh sách Sản phẩm')

@section('content')
<div class="container-fluid mt-4">
    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Danh sách Sản phẩm</h4>
                <a href="{{ route('admins.products.create') }}" class="btn btn-primary">
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
                                <button type="button" class="btn btn-sm btn-info view-product-btn" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#productDetailModal"
                                        data-url="{{ route('admins.products.show', $product->id) }}">
                                    <i class="ri-eye-line"></i> Xem
                                </button>
                                                            <a href="{{ route('admins.products.edit', $product->id) }}" class="btn btn-sm btn-warning">
                                    <i class="ri-pencil-line"></i> Sửa
                                </a>
                                <form action="{{ route('admins.products.destroy', $product->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này không?');">
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
<div class="modal fade" id="productDetailModal" tabindex="-1" aria-labelledby="productDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="productDetailModalLabel">Chi tiết sản phẩm</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="productDetailModalBody">
                {{-- Nội dung chi tiết sẽ được tải vào đây bằng JavaScript --}}
                <div class="text-center">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>
@endsection
@section('JS')
<script>
document.addEventListener("DOMContentLoaded", function() {
    // Lắng nghe sự kiện click trên tất cả các nút có class 'view-product-btn'
    document.querySelectorAll('.view-product-btn').forEach(button => {
        button.addEventListener('click', function() {
            const productUrl = this.dataset.url; // Lấy URL từ thuộc tính data-url
            const modalBody = document.getElementById('productDetailModalBody');
            
            // Hiển thị spinner loading
            modalBody.innerHTML = '<div class="text-center p-5"><div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div></div>';

            // Gọi API để lấy dữ liệu JSON
            fetch(productUrl)
                .then(response => response.json())
                .then(product => {
                    // Xây dựng nội dung HTML từ dữ liệu JSON nhận được
                    let html = `
                        <h4>${product.name} (#${product.code})</h4>
                        <hr>
                        <h6>Mô tả:</h6>
                        <div>${product.description || 'Không có mô tả.'}</div>
                        <hr>
                        <h6>Bộ sưu tập ảnh:</h6>
                        <div class="d-flex flex-wrap gap-2 mb-3">`;
                    
                    product.images.forEach(image => {
                        html += `<img src="/storage/${image.image_path}" class="img-thumbnail" style="width: 80px; height: 80px; object-fit: cover;">`;
                    });

                    html += `</div><hr><h6>Các phiên bản:</h6>`;

                    if (product.variants.length > 0) {
                        html += '<ul class="list-group">';
                        product.variants.forEach(variant => {
                            html += `
                                <li class="list-group-item">
                                    <strong>${variant.name}</strong><br>
                                    Giá: <span class="text-danger fw-bold">${new Intl.NumberFormat('vi-VN').format(variant.price)} VNĐ</span><br>
                                    Tồn kho: <span>${variant.stock}</span>
                                </li>`;
                        });
                        html += '</ul>';
                    } else {
                        html += '<p>Sản phẩm này chưa có phiên bản nào.</p>';
                    }

                    // Đổ nội dung HTML vào modal body
                    modalBody.innerHTML = html;
                })
                .catch(error => {
                    console.error('Error fetching product details:', error);
                    modalBody.innerHTML = '<div class="alert alert-danger">Không thể tải dữ liệu chi tiết.</div>';
                });
        });
    });
});
</script>
@endsection