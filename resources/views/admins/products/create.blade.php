@extends('layouts.admin')

@section('title', 'Thêm Sản Phẩm Mới')

@section('content')
<div class="container-fluid mt-4">
    <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data">
        @csrf

        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="mb-0">Thêm Sản Phẩm Mới</h4>
                    <div>
                        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                            <i class="ri-arrow-left-line"></i> Quay lại
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="ri-save-line"></i> Lưu sản phẩm
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Khối hiển thị lỗi validation và lỗi server --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Rất tiếc! Đã có lỗi xảy ra. Vui lòng kiểm tra lại các trường dưới đây.</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if (session('error'))
             <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <div class="row">
            {{-- CỘT TRÁI - THÔNG TIN CHÍNH --}}
            <div class="col-lg-8">
                <div class="card mb-3 shadow-sm">
                    <div class="card-header bg-white"><h5 class="mb-0">Thông tin sản phẩm</h5></div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="name" class="form-label">Tên sản phẩm <span class="text-danger">*</span></label>
                            <input type="text" id="name" name="name" class="form-control" value="{{ old('name') }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Mô tả</label>
                            <textarea id="description" name="description" class="form-control" rows="5">{{ old('description') }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="card mb-3 shadow-sm">
                    <div class="card-header bg-white"><h5 class="mb-0">Ảnh sản phẩm</h5></div>
                    <div class="card-body">
                        <input type="file" name="images[]" id="productImages" class="form-control" multiple>
                        <div id="imagePreview" class="mt-3 d-flex flex-wrap gap-2"></div>
                    </div>
                </div>

                <div class="card mb-3 shadow-sm">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Biến thể</h5>
                        <button type="button" class="btn btn-sm btn-outline-primary" id="add-variant">+ Thêm biến thể</button>
                    </div>
                    <div class="card-body">
                        <div id="variant-list">
                            {{-- Các biến thể sẽ được thêm vào đây bằng JS --}}
                        </div>
                    </div>
                </div>
            </div>

            {{-- CỘT PHẢI - THÔNG TIN PHỤ --}}
            <div class="col-lg-4">
                <div class="card mb-3 shadow-sm">
                    <div class="card-header bg-white"><h5 class="mb-0">Giá & Tồn kho</h5></div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="price" class="form-label">Giá cơ bản (VNĐ)</label>
                            <input type="number" id="price" name="price" class="form-control" value="{{ old('price') }}" placeholder="Nếu sản phẩm có biến thể, giá này sẽ là mặc định">
                        </div>
                        <div>
                            <label for="stock" class="form-label">Tồn kho cơ bản</label>
                            <input type="number" id="stock" name="stock" class="form-control" value="{{ old('stock') }}" placeholder="Tồn kho cho sản phẩm không có biến thể">
                        </div>
                    </div>
                </div>

                <div class="card mb-3 shadow-sm">
                    <div class="card-header bg-white"><h5 class="mb-0">Phân loại</h5></div>
                    <div class="card-body">
                        <label for="categories" class="form-label">Danh mục sản phẩm</label>
                        <select name="categories[]" id="categories" class="form-select" multiple>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ in_array($cat->id, old('categories', [])) ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text">Giữ Ctrl hoặc Cmd để chọn nhiều mục.</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-end mt-3">
             <button type="submit" class="btn btn-primary"><i class="ri-save-line"></i> Lưu sản phẩm</button>
        </div>
    </form>
</div>
@endsection

@section('JS')
{{-- Thư viện TinyMCE --}}
<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>

<script>
document.addEventListener("DOMContentLoaded", function () {
    // 1. Khởi tạo TinyMCE cho ô mô tả
    tinymce.init({
        selector: 'textarea#description',
        plugins: 'advlist autolink lists link image charmap preview anchor searchreplace visualblocks code fullscreen insertdatetime media table code help wordcount',
        toolbar: 'undo redo | blocks | bold italic backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help'
    });

    // 2. Xem trước ảnh sản phẩm
    const imageInput = document.getElementById('productImages');
    const imagePreview = document.getElementById('imagePreview');
    if (imageInput) {
        imageInput.addEventListener('change', function(event) {
            imagePreview.innerHTML = ''; // Xóa ảnh cũ
            for (const file of event.target.files) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.style.width = '100px';
                    img.style.height = '100px';
                    img.style.objectFit = 'cover';
                    img.className = 'img-thumbnail';
                    imagePreview.appendChild(img);
                }
                reader.readAsDataURL(file);
            }
        });
    }

    // ==========================================================
    // === BẮT ĐẦU PHẦN SỬA LỖI JAVASCRIPT CHO BIẾN THỂ ==========
    // ==========================================================
    let variantIndex = 0;
    const attributes = @json($attributes ?? []);
    const addBtn = document.getElementById('add-variant');
    const variantList = document.getElementById('variant-list');

    if (addBtn && variantList) {
        addBtn.addEventListener('click', function () {
            const variantId = `variant-wrapper-${variantIndex}`;
            const wrapper = document.createElement('div');
            wrapper.className = "card card-body mb-3";
            wrapper.id = variantId;

            // ... code tạo attributeSelectors giữ nguyên ...
            let attributeSelectors = '';
            if (attributes.length > 0) {
                attributes.forEach(attr => {
                    attributeSelectors += `
                        <div class="col-md-6 mb-2">
                            <label class="form-label small">${attr.name}</label>
                            <select name="variants[${variantIndex}][attribute_value_ids][]" class="form-select form-select-sm">
                                <option value="">-- Không chọn --</option>
                                ${attr.values.map(v => `<option value="${v.id}">${v.value}</option>`).join('')}
                            </select>
                        </div>`;
                });
            }

            // HTML hoàn chỉnh cho một biến thể
            const html = `
                <div class="d-flex justify-content-end mb-2">
                    <button type="button" class="btn-close" aria-label="Xóa biến thể" onclick="document.getElementById('${variantId}').remove();"></button>
                </div>
                <div class="row">
                    <!-- ========================================================== -->
                    <!-- === THÊM Ô NHẬP TÊN BIẾN THỂ ============================== -->
                    <!-- ========================================================== -->
                    <div class="col-12 mb-2">
                        <label class="form-label small">Tên biến thể (ví dụ: Size L, Màu Đỏ) <span class="text-danger">*</span></label>
                        <input type="text" name="variants[${variantIndex}][name]" class="form-control form-control-sm" placeholder="Nhập tên cho biến thể này" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label small">Giá biến thể <span class="text-danger">*</span></label>
                        <input type="number" name="variants[${variantIndex}][price]" class="form-control form-control-sm" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small">Tồn kho <span class="text-danger">*</span></label>
                        <input type="number" name="variants[${variantIndex}][stock]" class="form-control form-control-sm" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small">Ảnh biến thể</label>
                        <input type="file" name="variants[${variantIndex}][images][]" class="form-control form-control-sm" multiple>
                    </div>
                </div>
                <div class="row mt-2">
                    <label class="form-label small">Thuộc tính bổ sung (tùy chọn)</label>
                    ${attributeSelectors}
                </div>
            `;

            wrapper.innerHTML = html;
            variantList.appendChild(wrapper);
            variantIndex++;
        });
    }
});
</script>
@endsection