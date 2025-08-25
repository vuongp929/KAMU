@extends('layouts.admin')

@section('title', 'Thêm Sản Phẩm Mới')

@section('content')
<div class="container-fluid mt-4">
    <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data">
        @csrf

        {{-- Thanh tác vụ trên cùng --}}
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="mb-0">Thêm Sản Phẩm Mới</h4>
                    <div>
                        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary"><i class="ri-arrow-left-line"></i> Quay lại</a>
                        <button type="submit" class="btn btn-primary"><i class="ri-save-line"></i> Lưu sản phẩm</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Hiển thị lỗi --}}
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
                        <div class="mb-3"><label for="name" class="form-label">Tên sản phẩm <span class="text-danger">*</span></label><input type="text" id="name" name="name" class="form-control" value="{{ old('name') }}" required></div>
                        <div class="mb-3"><label for="product-description" class="form-label">Mô tả</label><textarea id="product-description" name="description" class="form-control">{{ old('description') }}</textarea></div>
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
                        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#attributesModal"><i class="ri-settings-3-line"></i> Chỉnh sửa thuộc tính & Tạo phiên bản</button>
                        <hr>
                        <h6>Danh sách phiên bản <span class="text-danger">*</span></h6>
                        <div id="variant-combinations-list" class="table-responsive"><p class="text-muted">Chưa có phiên bản nào được tạo.</p></div>
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
                                <option value="{{ $cat->id }}" {{ in_array($cat->id, old('categories', [])) ? 'selected' : '' }}>{{ $cat->name }}</option>
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
            <div class="modal-header"><h5 class="modal-title">Chọn thuộc tính cho sản phẩm</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                 @forelse($attributes as $attribute)
                    <div class="mb-3"><h6>{{ $attribute->name }}</h6><div class="d-flex flex-wrap gap-2">@foreach($attribute->values as $value)<div class="form-check"><input class="form-check-input attribute-value-checkbox" type="checkbox" value="{{ $value->id }}" id="value-{{ $value->id }}" data-attribute-id="{{ $attribute->id }}"><label class="form-check-label" for="value-{{ $value->id }}">{{ $value->value }}</label></div>@endforeach</div></div>
                 @empty
                    <p>Chưa có thuộc tính nào.</p>
                 @endforelse
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-primary" id="generate-variants-btn">Tạo các phiên bản</button></div>
        </div>
    </div>
</div>
@endsection

{{-- === BẮT ĐẦU PHẦN SCRIPT ĐÚNG === --}}
@push('scripts')
<script src="https://cdn.tiny.cloud/1/nlqml8ithb5bt0vy7jx4n9e1ycdpr85hwsn9kj6siz3uf10j/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    console.log('DOM loaded, initializing...');
    
    // Debug: Kiểm tra dữ liệu attributes
    const attributesData = @json($attributes);
    console.log('Attributes data:', attributesData);
    console.log('Number of attributes:', attributesData.length);
    
    tinymce.init({
        selector: 'textarea#product-description',
        plugins: 'advlist autolink lists link image charmap preview anchor searchreplace visualblocks code fullscreen insertdatetime media table help wordcount',
        toolbar: 'undo redo | blocks | bold italic backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help',
    });

    // Xử lý biến thể
    const generateBtn = document.getElementById('generate-variants-btn');
    const variantListContainer = document.getElementById('variant-combinations-list');
    const modalElement = document.getElementById('attributesModal');
    
    console.log('Elements found:', {
        generateBtn: !!generateBtn,
        variantListContainer: !!variantListContainer,
        modalElement: !!modalElement,
        bootstrap: typeof bootstrap !== 'undefined'
    });
    
    if (!generateBtn) {
        console.error('Generate button not found!');
        return;
    }
    
    if (!modalElement) {
        console.error('Modal element not found!');
        return;
    }
    
    let attributesModal;
    try {
        // Thử Bootstrap 5 trước
        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            attributesModal = new bootstrap.Modal(modalElement);
            console.log('Using Bootstrap 5 Modal');
        } else if (typeof $ !== 'undefined') {
            // Fallback cho Bootstrap 3/4 với jQuery
            attributesModal = {
                show: function() { $(modalElement).modal('show'); },
                hide: function() { $(modalElement).modal('hide'); }
            };
            console.log('Using jQuery Modal fallback');
        } else {
            console.error('Neither Bootstrap nor jQuery available');
            return;
        }
    } catch (error) {
        console.error('Error initializing modal:', error);
        // Fallback cuối cùng
        if (typeof $ !== 'undefined') {
            attributesModal = {
                show: function() { $(modalElement).modal('show'); },
                hide: function() { $(modalElement).modal('hide'); }
            };
            console.log('Using jQuery Modal as final fallback');
        } else {
            return;
        }
    }

    generateBtn.addEventListener('click', function() {
        console.log('Generate button clicked!');
        
        const selectedValues = {};
        const selectedValueObjects = {};

        const checkedBoxes = document.querySelectorAll('.attribute-value-checkbox:checked');
        console.log('Checked boxes found:', checkedBoxes.length);
        
        if (checkedBoxes.length === 0) {
            alert('Vui lòng chọn ít nhất một giá trị thuộc tính để tạo phiên bản!');
            console.log('No attributes selected, stopping generation');
            return;
        }
        
        checkedBoxes.forEach(checkbox => {
            const attributeId = checkbox.dataset.attributeId;
            const valueId = checkbox.value;
            const valueName = checkbox.nextElementSibling.textContent.trim();

            if (!selectedValuesByAttribute[attributeId]) {
                selectedValuesByAttribute[attributeId] = [];
            }
            selectedValuesByAttribute[attributeId].push({ id: valueId, name: valueName });
        });

        const combinations = generateCombinations(Object.values(selectedValueObjects));
        console.log('Generated combinations:', combinations);

        variantListContainer.innerHTML = '';

        if (combinations.length === 0) {
            variantListContainer.innerHTML = '<div class="alert alert-warning">Không thể tạo phiên bản. Vui lòng chọn ít nhất một giá trị từ mỗi thuộc tính.</div>';
            console.log('No combinations generated');
            attributesModal.hide();
            return;
        }
        
        console.log('Creating table with', combinations.length, 'variants');

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
                <td><input type="number" name="variants[${index}][price]" class="form-control form-control-sm" placeholder="Giá" required style="background-color: white !important; opacity: 1 !important;"></td>
                <td><input type="number" name="variants[${index}][stock]" class="form-control form-control-sm" placeholder="SL" required style="background-color: white !important; opacity: 1 !important;"></td>
                <td><input type="file" name="variants[${index}][image]" class="form-control form-control-sm" style="background-color: white !important; opacity: 1 !important;"></td>
            `;
            tbody.appendChild(row);
            
            // Debug: Kiểm tra input sau khi tạo
            console.log('Created row for variant:', variantName);
            const inputs = row.querySelectorAll('input[type="number"], input[type="file"]');
            inputs.forEach((input, inputIndex) => {
                console.log(`Input ${inputIndex}:`, {
                    disabled: input.disabled,
                    readonly: input.readOnly,
                    style: input.style.cssText,
                    computedStyle: window.getComputedStyle(input)
                });
            });
        });
        table.appendChild(tbody);
        variantListContainer.appendChild(table);
        
        console.log('Table created and appended successfully');
        console.log('Closing modal...');
        
        // Đóng modal với delay nhỏ để đảm bảo table đã render xong
        setTimeout(function() {
            attributesModal.hide();
            
            // Xóa backdrop và overlay để tránh web bị xám
            setTimeout(function() {
                const backdrops = document.querySelectorAll('.modal-backdrop');
                backdrops.forEach(backdrop => backdrop.remove());
                
                // Xóa class modal-open khỏi body
                document.body.classList.remove('modal-open');
                document.body.style.overflow = '';
                document.body.style.paddingRight = '';
                
                console.log('Modal backdrop cleaned up');
            }, 50);
            
            console.log('Modal closed successfully');
        }, 100);
        
        console.log('Variants generation completed!');
        
        // Thêm CSS để đảm bảo input không bị disable
         const style = document.createElement('style');
         style.textContent = `
             #variant-list input[type="number"],
             #variant-list input[type="file"] {
                 background-color: white !important;
                 opacity: 1 !important;
                 pointer-events: auto !important;
                 cursor: auto !important;
                 color: #495057 !important;
                 border: 1px solid #ced4da !important;
             }
             #variant-list input[type="number"]:focus,
             #variant-list input[type="file"]:focus {
                 border-color: #80bdff !important;
                 box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25) !important;
             }
             #variant-list input[type="number"]:disabled,
             #variant-list input[type="file"]:disabled {
                 background-color: #e9ecef !important;
                 opacity: 0.65 !important;
             }
         `;
         document.head.appendChild(style);
         
         // Force enable tất cả input sau khi tạo
          setTimeout(function() {
              const allInputs = document.querySelectorAll('#variant-list input[type="number"], #variant-list input[type="file"]');
              allInputs.forEach(input => {
                  input.disabled = false;
                  input.readOnly = false;
                  input.style.pointerEvents = 'auto';
                  input.style.cursor = 'auto';
                  
                  // Thêm event listeners để test
                  input.addEventListener('click', function() {
                      console.log('Input clicked:', this.name);
                  });
                  
                  input.addEventListener('focus', function() {
                      console.log('Input focused:', this.name);
                      this.style.backgroundColor = 'white';
                      this.style.opacity = '1';
                  });
                  
                  console.log('Force enabled input:', input.name, {
                      disabled: input.disabled,
                      readOnly: input.readOnly,
                      style: input.style.cssText
                  });
              });
          }, 100);
        
        // Scroll xuống để user thấy table vừa tạo
        setTimeout(function() {
            variantListContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }, 300);
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

    // Frontend validation
    document.querySelector('form').addEventListener('submit', function(e) {
        let isValid = true;
        let errorMessages = [];

        // Validate product name
        const productName = document.querySelector('input[name="name"]');
        if (!productName.value.trim()) {
            errorMessages.push('Tên sản phẩm không được để trống.');
            isValid = false;
        }

        // Validate categories
        const selectedCategories = document.querySelectorAll('input[name="categories[]"][type="checkbox"]:checked');
        if (selectedCategories.length === 0) {
            errorMessages.push('Vui lòng chọn ít nhất một danh mục.');
            isValid = false;
        }

        // Validate images
        const imageFiles = document.querySelector('input[name="images[]"]').files;
        if (imageFiles.length === 0) {
            errorMessages.push('Vui lòng chọn ít nhất một ảnh sản phẩm.');
            isValid = false;
        } else {
            // Validate image formats
            const allowedFormats = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp'];
            for (let file of imageFiles) {
                if (!allowedFormats.includes(file.type)) {
                    errorMessages.push(`Ảnh "${file.name}" không đúng định dạng. Chỉ chấp nhận: jpeg, png, jpg, gif, webp.`);
                    isValid = false;
                }
                if (file.size > 2048 * 1024) { // 2MB
                    errorMessages.push(`Ảnh "${file.name}" quá lớn. Kích thước tối đa: 2MB.`);
                    isValid = false;
                }
            }
        }

        // Validate variants
        const variantPrices = document.querySelectorAll('input[name*="[price]"]');
        const variantStocks = document.querySelectorAll('input[name*="[stock]"]');
        
        if (variantPrices.length === 0) {
            errorMessages.push('Vui lòng tạo ít nhất một phiên bản sản phẩm.');
            isValid = false;
        } else {
            variantPrices.forEach((priceInput, index) => {
                if (!priceInput.value || parseFloat(priceInput.value) < 0) {
                    errorMessages.push(`Giá phiên bản ${index + 1} không hợp lệ.`);
                    isValid = false;
                }
            });
            
            variantStocks.forEach((stockInput, index) => {
                if (!stockInput.value || parseInt(stockInput.value) < 0) {
                    errorMessages.push(`Số lượng phiên bản ${index + 1} không hợp lệ.`);
                    isValid = false;
                }
            });
        }

        // Validate variant images if any
        const variantImageInputs = document.querySelectorAll('input[name*="[image]"][type="file"]');
        variantImageInputs.forEach((input, index) => {
            if (input.files.length > 0) {
                const file = input.files[0];
                const allowedFormats = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp'];
                if (!allowedFormats.includes(file.type)) {
                    errorMessages.push(`Ảnh phiên bản ${index + 1} không đúng định dạng.`);
                    isValid = false;
                }
                if (file.size > 2048 * 1024) {
                    errorMessages.push(`Ảnh phiên bản ${index + 1} quá lớn (tối đa 2MB).`);
                    isValid = false;
                }
            }
        });

        if (!isValid) {
            e.preventDefault();
            alert('Lỗi validation:\n\n' + errorMessages.join('\n'));
            return false;
        }

        return true;
    });
});
</script>
@endpush
{{-- === KẾT THÚC PHẦN SCRIPT ĐÚNG === --}}