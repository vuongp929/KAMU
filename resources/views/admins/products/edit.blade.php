@extends('layouts.admin')

@section('title', 'Chỉnh Sửa: ' . $product->name)

@section('content')
<div class="container-fluid mt-4">
    {{-- Sử dụng route model binding, truyền thẳng object $product --}}
    <form method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data">
    {{-- <form method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data">
 origin/main --}}
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
                        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#attributesModal" onclick="openAttributesModal()">
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

<div class="modal fade" id="attributesModal" tabindex="-1" aria-labelledby="attributesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title" id="attributesModalLabel">Chọn thuộc tính</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div>
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
<style>
/* Đảm bảo scroll hoạt động bình thường */
html, body {
    overflow-x: auto !important;
    overflow-y: auto !important;
}

/* Khi modal mở, chỉ ẩn scroll của body, không ảnh hưởng đến toàn bộ trang */
body.modal-open {
    overflow: hidden;
    padding-right: 0 !important;
}

/* Đảm bảo modal không ảnh hưởng đến scroll sau khi đóng */
.modal {
    overflow-y: auto;
}

.modal-backdrop {
    position: fixed;
}
</style>

<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>

<script>
// Global function để mở modal
function openAttributesModal() {

    const modalElement = document.getElementById('attributesModal');
    if (modalElement) {
        try {
            // Thử dùng Bootstrap 5
            if (typeof bootstrap !== 'undefined') {
                const modal = new bootstrap.Modal(modalElement);
                modal.show();

            } else if (typeof $ !== 'undefined') {
                // Fallback jQuery
                $(modalElement).modal('show');

            } else {
                console.error('Neither Bootstrap nor jQuery available');
            }
        } catch (error) {
            console.error('Error opening modal:', error);
        }
    } else {
        console.error('Modal element not found');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    let existingVariants = @json($product->variants->load('attributeValues', 'images'));
    
    // Debug: Kiểm tra dữ liệu variants và images
    console.log('Existing variants data:', existingVariants);
    if (existingVariants.length > 0) {
        console.log('First variant images:', existingVariants[0].images);
    }
    const allAttributes = @json($attributes->load('values'));
    
    // Khởi tạo TinyMCE
    tinymce.init({
        selector: 'textarea#product-description',
        height: 300,
        menubar: false,
        plugins: 'advlist autolink lists link image charmap print preview anchor searchreplace visualblocks code fullscreen insertdatetime media table paste code help wordcount',
        toolbar: 'undo redo | formatselect | bold italic backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help'
    });
    
    // Khởi tạo các element DOM
    const generateBtn = document.getElementById('generate-variants-btn');
    const variantListContainer = document.getElementById('variant-combinations-list');
    const modalElement = document.getElementById('attributesModal');
    
    // Render bảng biến thể ngay khi trang load nếu có dữ liệu (sau khi khởi tạo DOM elements)
    if (existingVariants && existingVariants.length > 0) {
        console.log('Rendering existing variants on page load');
        renderVariantTable(existingVariants);
    }
    

    
    // JavaScript đã được khởi tạo thành công
    
    if (!generateBtn) {
        console.error('Generate button not found! Check if ID is correct.');
    } else {

    }
    
    if (!modalElement) {
        console.error('Modal element not found!');
        return;
    }
    
    // Thử khởi tạo modal theo cách khác
    let attributesModal;
    try {
        attributesModal = new bootstrap.Modal(modalElement);

    } catch (error) {
        console.error('Error initializing modal:', error);
        // Fallback: sử dụng jQuery nếu Bootstrap 5 không hoạt động
        if (typeof $ !== 'undefined') {

            attributesModal = {
                show: function() { $(modalElement).modal('show'); },
                hide: function() { $(modalElement).modal('hide'); }
            };
        } else {
            console.error('Neither Bootstrap 5 nor jQuery available');
            return;
        }
    }

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
            
            // Xử lý ảnh biến thể - kiểm tra cả images array và image object
            let existingImageHtml = '';
            console.log(`Variant ${index} images:`, variant.images); // Debug log
            
            if (variant.images && Array.isArray(variant.images) && variant.images.length > 0) {
                const image = variant.images[0];
                console.log(`Variant ${index} first image:`, image); // Debug log
                
                // Kiểm tra các trường path có thể có
                const imagePath = image.path || image.image_path || image.url;
                
                if (imagePath) {
                    // Đảm bảo đường dẫn đúng format
                    const fullPath = imagePath.startsWith('/storage/') ? imagePath : `/storage/${imagePath}`;
                    existingImageHtml = `<img src="${fullPath}" class="img-thumbnail mt-2" style="width: 40px; height: 40px; object-fit: cover;" alt="Ảnh biến thể" onerror="this.style.display='none'">`;
                    console.log(`Variant ${index} image path:`, fullPath); // Debug log
                } else {
                    console.log(`Variant ${index} no valid image path found`); // Debug log
                }
            } else {
                console.log(`Variant ${index} no images array or empty`); // Debug log
            }

            const row = document.createElement('tr');
            row.innerHTML = `
                <td>
                    <strong>${variantName}</strong>
                    ${variant.id ? `<input type="hidden" name="variants[${index}][id]" value="${variant.id}">` : ''}
                    ${attributeValueIds.map(id => `<input type="hidden" name="variants[${index}][attribute_value_ids][]" value="${id}">`).join('')}
                    <input type="hidden" name="variants[${index}][name]" value="${variantName}">
                </td>
                <td><input type="number" name="variants[${index}][price]" class="form-control form-control-sm" value="${variantPrice}" required></td>
                <td><input type="number" name="variants[${index}][stock]" class="form-control form-control-sm" value="${variantStock}" required></td>
                <td>
                    <input type="file" name="variants[${index}][image]" class="form-control form-control-sm" accept="image/*">
                    ${existingImageHtml}
                    ${existingImageHtml ? '<div class="text-muted small mt-1">Ảnh hiện tại</div>' : '<div class="text-muted small mt-1">Chưa có ảnh</div>'}
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

    // Đợi một chút để đảm bảo modal đã được render
    setTimeout(function() {
        const generateBtn = document.getElementById('generate-variants-btn');

        
        if (generateBtn) {

        
        generateBtn.addEventListener('click', function(e) {
            e.preventDefault();

            
            const selectedValues = {};
            const checkedBoxes = document.querySelectorAll('.attribute-value-checkbox:checked');

            
            checkedBoxes.forEach(checkbox => {
                const attributeId = checkbox.dataset.attributeId;
                if (!selectedValues[attributeId]) {
                    selectedValues[attributeId] = [];
                }
                selectedValues[attributeId].push({ id: checkbox.value, name: checkbox.nextElementSibling.textContent.trim() });
            });
            

            
            if (Object.keys(selectedValues).length === 0) {
                alert('Vui lòng chọn ít nhất một thuộc tính!');
                return;
            }

            const combinations = generateCombinations(Object.values(selectedValues));

            
            // Tạo map từ existing variants để giữ lại dữ liệu
            const existingVariantMap = new Map();
            existingVariants.forEach(variant => {
                const key = variant.attribute_values.map(v => v.id).sort().join('-');
                existingVariantMap.set(key, variant);
            });
            
            const newVariantsData = combinations.map(combo => {
                const key = combo.map(v => v.id).sort().join('-');
                const existingVariant = existingVariantMap.get(key);
                
                return {
                    id: existingVariant?.id || null,
                    name: combo.map(v => v.name).join(' / '),
                    price: existingVariant?.price || '',
                    stock: existingVariant?.stock || '',
                    attribute_values: combo,
                    images: existingVariant?.images || [],
                };
            });
            

            
            // Cập nhật existingVariants để sử dụng cho lần sau
            existingVariants = newVariantsData;
            
            renderVariantTable(newVariantsData);
            
            // Đóng modal và dọn dẹp backdrop
            try {
                attributesModal.hide();
            } catch (error) {
                console.error('Error hiding modal:', error);
                // Fallback
                if (typeof $ !== 'undefined') {
                    $(modalElement).modal('hide');
                }
            }
            
            // Dọn dẹp modal backdrop nhẹ nhàng
            setTimeout(() => {
                // Xóa tất cả modal backdrop
                const backdrops = document.querySelectorAll('.modal-backdrop');
                backdrops.forEach(backdrop => {
                    backdrop.remove();
                });
                
                // Reset modal classes và khôi phục scroll
                document.body.classList.remove('modal-open');
                document.documentElement.classList.remove('modal-open');
                
                // Khôi phục scroll hoàn toàn
                document.body.style.overflow = '';
                document.body.style.paddingRight = '';
                document.body.style.marginRight = '';
                
                // Đảm bảo html cũng có thể scroll
                document.documentElement.style.overflow = '';
                
                // Scroll đến bảng biến thể sau khi modal đã đóng hoàn toàn
                setTimeout(() => {
                    const variantTable = document.getElementById('variant-combinations-list');
                    if (variantTable) {
                        variantTable.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                }, 50);
            }, 100);
            
            // Backup cleanup để đảm bảo scroll hoạt động
            setTimeout(() => {
                const remainingBackdrops = document.querySelectorAll('.modal-backdrop');
                if (remainingBackdrops.length > 0) {
                    remainingBackdrops.forEach(backdrop => backdrop.remove());
                }
                
                // Đảm bảo scroll được khôi phục hoàn toàn
                document.body.classList.remove('modal-open');
                document.documentElement.classList.remove('modal-open');
                document.body.style.overflow = '';
                document.body.style.paddingRight = '';
                document.documentElement.style.overflow = '';
            }, 500);
        });
    } else {
        console.error('Cannot add event listener: Generate button not found!');
    }
    }, 1000); // Đợi 1 giây

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

        // Validate variants
        const variantPrices = document.querySelectorAll('input[name*="[price]"]');
        const variantStocks = document.querySelectorAll('input[name*="[stock]"]');
        
        if (variantPrices.length === 0) {
            errorMessages.push('Sản phẩm phải có ít nhất một phiên bản.');
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

        // Validate new images if any
        const imageFiles = document.querySelector('input[name="images[]"]');
        if (imageFiles && imageFiles.files.length > 0) {
            const allowedFormats = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp'];
            for (let file of imageFiles.files) {
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
@endsection