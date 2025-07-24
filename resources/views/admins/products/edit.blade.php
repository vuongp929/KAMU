@extends('layouts.admin')

@section('title', 'Chỉnh Sửa Sản Phẩm: ' . $product->name)

@section('content')
<div class="container-fluid mt-4">
    {{-- Sử dụng route model binding, truyền thẳng object $product --}}
    <form method="POST" action="{{ route('admins.products.update', $product) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="mb-0">Chỉnh Sửa Sản Phẩm</h4>
                    <div>
                        <a href="{{ route('admins.products.index') }}" class="btn btn-secondary"><i class="ri-arrow-left-line"></i> Quay lại</a>
                        <button type="submit" class="btn btn-primary"><i class="ri-save-line"></i> Cập nhật</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Khối hiển thị lỗi --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Rất tiếc! Đã có lỗi xảy ra.</strong>
                <ul>@foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach</ul>
            </div>
        @endif
        @if (session('error'))
             <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="row">
            {{-- CỘT TRÁI --}}
            <div class="col-lg-8">
                <div class="card mb-3 shadow-sm">
                    <div class="card-header bg-white"><h5 class="mb-0">Thông tin sản phẩm</h5></div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="name" class="form-label">Tên sản phẩm <span class="text-danger">*</span></label>
                            {{-- old('name', $product->name) sẽ ưu tiên dữ liệu cũ nếu validation lỗi --}}
                            <input type="text" id="name" name="name" class="form-control" value="{{ old('name', $product->name) }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Mô tả</label>
                            <textarea id="description" name="description" class="form-control" rows="5">{{ old('description', $product->description) }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="card mb-3 shadow-sm">
                    <div class="card-header bg-white"><h5 class="mb-0">Ảnh sản phẩm</h5></div>
                    <div class="card-body">
                        <div class="mb-2">
                            <label for="images" class="form-label">Tải lên ảnh mới (sẽ được thêm vào bộ sưu tập)</label>
                            <input type="file" name="images[]" id="productImages" class="form-control" multiple>
                        </div>
                        <hr>
                        <h6>Các ảnh hiện tại:</h6>
                        <div id="existingImages" class="mt-2 d-flex flex-wrap gap-2">
                            {{-- Hiển thị các ảnh đã có --}}
                            @foreach($product->images as $image)
                                <div class="position-relative">
                                    <img src="{{ Storage::url($image->image_path) }}" class="img-thumbnail" style="width: 100px; height: 100px; object-fit: cover;">
                                    {{-- TODO: Thêm nút xóa ảnh nếu bạn muốn có chức năng này --}}
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="card mb-3 shadow-sm">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Biến thể</h5>
                        <button type="button" class="btn btn-sm btn-outline-primary" id="add-variant">+ Thêm biến thể</button>
                    </div>
                    <div class="card-body">
                        <p class="text-muted small">Lưu ý: Việc cập nhật sẽ xóa các biến thể cũ và tạo lại từ danh sách dưới đây.</p>
                        <div id="variant-list">
                            {{-- Hiển thị các biến thể đã có của sản phẩm --}}
                            {{-- Hiển thị các biến thể đã có của sản phẩm --}}
            @foreach($product->variants as $index => $variant)
                <div class="card card-body mb-3" id="variant-wrapper-{{$index}}">
                    <div class="d-flex justify-content-end mb-2">
                        <button type="button" class="btn-close" aria-label="Xóa biến thể" onclick="document.getElementById('variant-wrapper-{{$index}}').remove();"></button>
                    </div>
                    <div class="row">
                        <div class="col-12 mb-2">
                            <label class="form-label small">Tên biến thể <span class="text-danger">*</span></label>
                            <input type="text" name="variants[{{$index}}][name]" class="form-control form-control-sm" value="{{ old("variants.$index.name", $variant->name) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small">Giá <span class="text-danger">*</span></label>
                            <input type="number" name="variants[{{$index}}][price]" class="form-control form-control-sm" value="{{ old("variants.$index.price", $variant->price) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small">Tồn kho <span class="text-danger">*</span></label>
                            <input type="number" name="variants[{{$index}}][stock]" class="form-control form-control-sm" value="{{ old("variants.$index.stock", $variant->stock) }}" required>
                        </div>
                    </div>
                    <hr class="my-2">
                    
                    <div class="row mt-2">
                        <label class="form-label small">Thuộc tính đã chọn</label>
                        @foreach($attributes as $attribute)
                            <div class="col-md-6 mb-2">
                                <select name="variants[{{$index}}][attribute_value_ids][]" class="form-select form-select-sm">
                                    <option value="">-- Chọn {{ $attribute->name }} --</option>
                                    @foreach($attribute->values as $value)
                                        <option value="{{ $value->id }}" 
                                            {{ $variant->attributeValues->contains('id', $value->id) ? 'selected' : '' }}>
                                            {{ $value->value }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-2">
                        <label class="form-label small">Ảnh của biến thể (Tải lên ảnh mới sẽ ghi đè)</label>
                        <input type="file" name="variants[{{$index}}][images][]" class="form-control form-control-sm" multiple>
                        @if($variant->images->isNotEmpty())
                            <div class="d-flex flex-wrap gap-2 mt-2">
                                @foreach($variant->images as $image)
                                    {{-- SỬA LẠI TỪ 'path' THÀNH 'image_path' CHO KHỚP VỚI DATABASE --}}
                                    <img src="{{ Storage::url($image->image_path) }}" class="img-thumbnail" style="width: 60px; height: 60px; object-fit: cover;">
                                @endforeach
                            </div>
                        @endif
                    </div>

                </div>
            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- CỘT PHẢI --}}
            <div class="col-lg-4">
                 <div class="card mb-3 shadow-sm">
                    <div class="card-header bg-white"><h5 class="mb-0">Phân loại</h5></div>
                    <div class="card-body">
                        <label for="categories" class="form-label">Danh mục sản phẩm</label>
                        {{-- old('categories', $product->categories->pluck('id')->toArray()) sẽ lấy danh sách category cũ --}}
                        <select name="categories[]" id="categories" class="form-select" multiple>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ in_array($cat->id, old('categories', $product->categories->pluck('id')->toArray())) ? 'selected' : '' }}>
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
             <button type="submit" class="btn btn-primary"><i class="ri-save-line"></i> Cập nhật</button>
        </div>
    </form>
</div>
@endsection

@section('JS')
<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>

<script>
document.addEventListener("DOMContentLoaded", function () {
    // 1. Khởi tạo TinyMCE
    tinymce.init({
        selector: 'textarea#description',
        // ... các config khác của tinymce
    });

    // 2. Xử lý thêm biến thể mới (tương tự trang create)
    // Bắt đầu index từ số lượng biến thể đã có để không bị trùng
    let variantIndex = {{ $product->variants->count() }};
    const addBtn = document.getElementById('add-variant');
    const variantList = document.getElementById('variant-list');

    if (addBtn && variantList) {
        addBtn.addEventListener('click', function () {
            const variantId = `variant-wrapper-${variantIndex}`;
            const wrapper = document.createElement('div');
            wrapper.className = "card card-body mb-3";
            wrapper.id = variantId;

            // HTML cho biến thể mới hoàn toàn giống trang create
            const html = `
                <div class="d-flex justify-content-end mb-2">
                    <button type="button" class="btn-close" aria-label="Xóa biến thể" onclick="document.getElementById('${variantId}').remove();"></button>
                </div>
                <div class="row">
                    <div class="col-12 mb-2">
                        <label class="form-label small">Tên biến thể <span class="text-danger">*</span></label>
                        <input type="text" name="variants[${variantIndex}][name]" class="form-control form-control-sm" placeholder="Nhập tên cho biến thể mới" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small">Giá <span class="text-danger">*</span></label>
                        <input type="number" name="variants[${variantIndex}][price]" class="form-control form-control-sm" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small">Tồn kho <span class="text-danger">*</span></label>
                        <input type="number" name="variants[${variantIndex}][stock]" class="form-control form-control-sm" required>
                    </div>
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