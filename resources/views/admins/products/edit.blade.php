@extends('layouts.admin')

@section('title', 'Chỉnh Sửa: ' . $product->name)

@section('content')
<div class="container-fluid mt-4">
    <form method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="mb-0">Chỉnh Sửa Sản Phẩm</h4>
                    <div>
                        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary"><i class="ri-arrow-left-line"></i> Quay lại</a>
                        <button type="submit" class="btn btn-primary"><i class="ri-save-line"></i> Cập nhật</button>
                    </div>
                </div>
            </div>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Rất tiếc! Đã có lỗi xảy ra.</strong>
                <ul>@foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach</ul>
            </div>
        @endif

        <div class="row">
            {{-- CỘT TRÁI --}}
            <div class="col-lg-8">
                <div class="card mb-3 shadow-sm">
                    <div class="card-header bg-white"><h5 class="mb-0">Thông tin sản phẩm</h5></div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="name" class="form-label">Tên sản phẩm <span class="text-danger">*</span></label>
                            <input type="text" id="name" name="name" class="form-control" value="{{ old('name', $product->name) }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="product-description" class="form-label">Mô tả</label>
                            <textarea id="product-description" name="description" class="form-control">{{ old('description', $product->description) }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="card mb-3 shadow-sm">
                    <div class="card-header bg-white"><h5 class="mb-0">Bộ sưu tập ảnh</h5></div>
                    <div class="card-body">
                        <label class="form-label">Tải lên ảnh mới (sẽ thay thế toàn bộ ảnh cũ)</label>
                        <input type="file" name="images[]" class="form-control" multiple>
                        <hr>
                        <h6>Ảnh hiện tại:</h6>
                        <div class="d-flex flex-wrap gap-2">
                             @forelse($product->images as $image)
                                <img src="{{ Storage::url($image->image_path) }}" class="img-thumbnail" style="width: 80px; height: 80px; object-fit: cover;" title="Ảnh sản phẩm chính">
                            @empty
                                <p class="text-muted small">Chưa có ảnh chính.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="card mb-3 shadow-sm">
                    <div class="card-header bg-white"><h5 class="mb-0">Các phiên bản sản phẩm</h5></div>
                    <div class="card-body">
                        <p class="text-muted small">Mở cửa sổ để chỉnh sửa thuộc tính. Việc tạo lại sẽ ghi đè lên danh sách hiện tại.</p>
                        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#attributesModal">
                            <i class="ri-settings-3-line"></i> Chỉnh sửa thuộc tính & Tạo lại phiên bản
                        </button>
                        <hr>
                        <h6>Danh sách phiên bản <span class="text-danger">*</span></h6>
                        <div id="variant-combinations-list" class="table-responsive">

                        </div>
                    </div>
                </div>
            </div>

            {{-- CỘT PHẢI --}}
            <div class="col-lg-4">
                <div class="card mb-3 shadow-sm">
                    <div class="card-header bg-white"><h5 class="mb-0">Phân loại</h5></div>
                    <div class="card-body">
                        <label for="categories" class="form-label">Danh mục</label>
                        <select name="categories[]" id="categories" class="form-select" multiple>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ in_array($cat->id, old('categories', $product->categories->pluck('id')->toArray())) ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="text-end mt-3">
             <button type="submit" class="btn btn-primary"><i class="ri-save-line"></i> Cập nhật</button>
        </div>
    </form>
</div>

<div class="modal fade" id="attributesModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Chọn thuộc tính</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                @forelse($attributes as $attribute)
                    <div class="mb-3">
                        <h6>{{ $attribute->name }}</h6>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($attribute->values as $value)
                                <div class="form-check">
                                    <input class="form-check-input attribute-value-checkbox" type="checkbox" value="{{ $value->id }}" id="value-{{ $value->id }}" data-attribute-id="{{ $attribute->id }}">
                                    <label class="form-check-label" for="value-{{ $value->id }}">{{ $value->value }}</label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <p>Chưa có thuộc tính nào được tạo.</p>
                @endforelse
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="generate-variants-btn">Tạo lại các phiên bản</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('JS')
<script src="https://cdn.tiny.cloud/1/YOUR_API_KEY/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const existingVariants = @json($product->variants->load('attributeValues', 'images'));
    const allAttributes = @json($attributes);

    tinymce.init({ selector: 'textarea#product-description', /* ... */ });
    
    const generateBtn = document.getElementById('generate-variants-btn');
    const variantListContainer = document.getElementById('variant-combinations-list');
    const modalElement = document.getElementById('attributesModal');
    const attributesModal = bootstrap.Modal.getOrCreateInstance(modalElement);

    function renderVariantTable(variantsData) {
        variantListContainer.innerHTML = '';
        if (!variantsData || variantsData.length === 0) {
            variantListContainer.innerHTML = '<p class="text-muted">Chưa có phiên bản nào.</p>';
            return;
        }

        const table = document.createElement('table');
        table.className = 'table table-bordered';
        table.innerHTML = `
            <thead class="table-light">
                <tr>
                    <th style="width: 30%;">Tên phiên bản</th>
                    <th style="width: 25%;">Giá *</th>
                    <th style="width: 20%;">Tồn kho *</th>
                    <th style="width: 25%;">Ảnh đại diện</th>
                </tr>
            </thead>
        `;
        const tbody = document.createElement('tbody');
        
        variantsData.forEach((variant, index) => {
            const variantName = variant.name || 'N/A';
            const attributeValueIds = (variant.attribute_values || []).map(v => v.id);
            const variantPrice = variant.price || '';
            const variantStock = variant.stock || '';
            const existingImage = (variant.images && variant.images.length > 0) ? variant.images[0] : null;

            const row = document.createElement('tr');
            row.innerHTML = `
                <td>
                    <strong>${variantName}</strong>
                    ${attributeValueIds.map(id => `<input type="hidden" name="variants[${index}][attribute_value_ids][]" value="${id}">`).join('')}
                    <input type="hidden" name="variants[${index}][name]" value="${variantName}">
                </td>
                <td><input type="number" name="variants[${index}][price]" class="form-control form-control-sm" value="${variantPrice}" required></td>
                <td><input type="number" name="variants[${index}][stock]" class="form-control form-control-sm" value="${variantStock}" required></td>
                <td>
                    <input type="file" name="variants[${index}][image]" class="form-control form-control-sm">
                    ${existingImage ? `<img src="/storage/${existingImage.image_path}" class="img-thumbnail mt-2" style="width: 40px; height: 40px; object-fit: cover;">` : ''}
                </td>
            `;
            tbody.appendChild(row);
        });
        table.appendChild(tbody);
        variantListContainer.appendChild(table);
    }

    modalElement.addEventListener('show.bs.modal', function() {
        document.querySelectorAll('.attribute-value-checkbox').forEach(cb => cb.checked = false);
        
        const currentAttributeIds = new Set();
        existingVariants.forEach(variant => {
            variant.attribute_values.forEach(val => currentAttributeIds.add(val.id.toString()));
        });

        currentAttributeIds.forEach(id => {
            const checkbox = document.getElementById(`value-${id}`);
            if(checkbox) checkbox.checked = true;
        });
    });

    generateBtn.addEventListener('click', function() {
        const selectedValues = {};
        document.querySelectorAll('.attribute-value-checkbox:checked').forEach(checkbox => {
            const attributeId = checkbox.dataset.attributeId;
            if (!selectedValues[attributeId]) {
                selectedValues[attributeId] = [];
            }
            selectedValues[attributeId].push({ id: checkbox.value, name: checkbox.nextElementSibling.textContent.trim() });
        });

        const combinations = generateCombinations(Object.values(selectedValues));
        const newVariantsData = combinations.map(combo => ({
            name: combo.map(v => v.name).join(' / '),
            price: '', // Giá và tồn kho để trống cho người dùng nhập
            stock: '',
            attribute_values: combo,
            images: [], // Biến thể mới chưa có ảnh
        }));
        
        renderVariantTable(newVariantsData);
        attributesModal.hide();
    });

    // Hàm tạo tổ hợp (giữ nguyên)
    function generateCombinations(arrays, index = 0, current = []) {
        if (index === arrays.length) return [current];
        let result = [];
        arrays[index].forEach(item => {
            result = result.concat(generateCombinations(arrays, index + 1, [...current, item]));
        });
        return result;
    }

    renderVariantTable(existingVariants);
});
</script>
@endsection