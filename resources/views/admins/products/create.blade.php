@extends('layouts.admin')

@section('title', 'Thêm Sản Phẩm Mới')

@section('content')
<div class="container-fluid mt-4">
    <form method="POST" action="{{ route('admins.products.store') }}" enctype="multipart/form-data">
        @csrf

        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="mb-0">Thêm Sản Phẩm Mới</h4>
                    <div>
                        <a href="{{ route('admins.products.index') }}" class="btn btn-secondary"><i class="ri-arrow-left-line"></i> Quay lại</a>
                        <button type="submit" class="btn btn-primary"><i class="ri-save-line"></i> Lưu sản phẩm</button>
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
                            <input type="text" id="name" name="name" class="form-control" value="{{ old('name') }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="product-description" class="form-label">Mô tả</label>
                            <textarea id="product-description" name="description" class="form-control">{{ old('description') }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="card mb-3 shadow-sm">
                    <div class="card-header bg-white"><h5 class="mb-0">Bộ sưu tập ảnh</h5></div>
                    <div class="card-body">
                        <label class="form-label">Tải lên các ảnh <span class="text-danger">*</span></label>
                        <input type="file" name="images[]" class="form-control" multiple required>
                        <div class="form-text">Ảnh đầu tiên bạn chọn sẽ được dùng làm ảnh đại diện.</div>
                    </div>
                </div>

                <div class="card mb-3 shadow-sm">
                    <div class="card-header bg-white"><h5 class="mb-0">Các phiên bản sản phẩm (Biến thể)</h5></div>
                    <div class="card-body">
                        <p class="text-muted small">Chọn các thuộc tính để hệ thống tự động tạo ra các phiên bản tương ứng.</p>
                        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#attributesModal">
                            <i class="ri-settings-3-line"></i> Chỉnh sửa thuộc tính & Tạo phiên bản
                        </button>
                        <hr>
                        <h6>Danh sách phiên bản <span class="text-danger">*</span></h6>
                        <div id="variant-combinations-list" class="table-responsive">
                            <p class="text-muted">Chưa có phiên bản nào được tạo.</p>
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
                        <select name="categories[]" id="categories" class="form-select" multiple>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ in_array($cat->id, old('categories', [])) ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-end mt-3">
             <button type="submit" class="btn btn-primary"><i class="ri-save-line"></i> Lưu sản phẩm</button>
        </div>
    </form>
</div>

<!-- Modal để chọn thuộc tính -->
<div class="modal fade" id="attributesModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Chọn thuộc tính cho sản phẩm</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
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
                    <p>Chưa có thuộc tính nào.</p>
                 @endforelse
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="generate-variants-btn">Tạo các phiên bản</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('JS')
<script src="https://cdn.tiny.cloud/1/nlqml8ithb5bt0vy7jx4n9e1ycdpr85hwsn9kj6siz3uf10j/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    tinymce.init({
        selector: 'textarea#product-description',
        plugins: 'advlist autolink lists link image charmap preview anchor searchreplace visualblocks code fullscreen insertdatetime media table help wordcount',
        toolbar: 'undo redo | blocks | bold italic backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help',
    });

    const generateBtn = document.getElementById('generate-variants-btn');
    const variantListContainer = document.getElementById('variant-combinations-list');
    const modalElement = document.getElementById('attributesModal');
    const attributesModal = bootstrap.Modal.getOrCreateInstance(modalElement);

    generateBtn.addEventListener('click', function() {
        const selectedValues = {};
        const selectedValueObjects = {};

        document.querySelectorAll('.attribute-value-checkbox:checked').forEach(checkbox => {
            const attributeId = checkbox.dataset.attributeId;
            const valueId = checkbox.value;
            const valueName = checkbox.nextElementSibling.textContent.trim();

            if (!selectedValues[attributeId]) {
                selectedValues[attributeId] = [];
                selectedValueObjects[attributeId] = [];
            }
            selectedValues[attributeId].push(valueId);
            selectedValueObjects[attributeId].push({ id: valueId, name: valueName });
        });

        const combinations = generateCombinations(Object.values(selectedValueObjects));

        variantListContainer.innerHTML = '';
        if (combinations.length === 0) {
            variantListContainer.innerHTML = '<p class="text-muted">Vui lòng chọn thuộc tính để tạo phiên bản.</p>';
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
        combinations.forEach((combo, index) => {
            const variantName = combo.map(v => v.name).join(' / ');
            const attributeValueIds = combo.map(v => v.id);

            const row = document.createElement('tr');
            row.innerHTML = `
                <td>
                    <strong>${variantName}</strong>
                    ${attributeValueIds.map(id => `<input type="hidden" name="variants[${index}][attribute_value_ids][]" value="${id}">`).join('')}
                    <input type="hidden" name="variants[${index}][name]" value="${variantName}">
                </td>
                <td><input type="number" name="variants[${index}][price]" class="form-control form-control-sm" placeholder="Giá" required></td>
                <td><input type="number" name="variants[${index}][stock]" class="form-control form-control-sm" placeholder="SL" required></td>
                <td><input type="file" name="variants[${index}][image]" class="form-control form-control-sm"></td>
            `;
            tbody.appendChild(row);
        });
        table.appendChild(tbody);
        variantListContainer.appendChild(table);

        attributesModal.hide();
    });

    function generateCombinations(arrays, index = 0, current = []) {
        if (index === arrays.length) {
            return [current];
        }
        let result = [];
        arrays[index].forEach(item => {
            result = result.concat(generateCombinations(arrays, index + 1, [...current, item]));
        });
        return result;
    }
});
</script>
@endsection