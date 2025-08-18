// Shopee-style Discount Modal JavaScript
class DiscountModal {
    constructor() {
        this.selectedDiscount = null;
        this.appliedDiscount = null;
        this.discountCodes = [
            {
                code: 'KUMA10',
                title: 'Giảm 10%',
                description: 'Giảm 10% cho đơn hàng từ 100.000đ',
                discount_type: 'percentage',
                discount_value: 10,
                min_order_value: 100000
            },
            {
                code: 'FREESHIP',
                title: 'Miễn phí vận chuyển',
                description: 'Miễn phí vận chuyển cho đơn hàng từ 200.000đ',
                discount_type: 'fixed',
                discount_value: 30000,
                min_order_value: 200000
            },
            {
                code: 'SAVE50K',
                title: 'Giảm 50.000đ',
                description: 'Giảm 50.000đ cho đơn hàng từ 500.000đ',
                discount_type: 'fixed',
                discount_value: 50000,
                min_order_value: 500000
            }
        ];
        this.isLoading = false;
        
        this.init();
    }

    init() {
        this.bindEvents();
        this.loadAppliedDiscount();
        this.renderDiscountList();
        this.updateAppliedDiscountDisplay();
        // this.loadVouchersFromAPI(); // Tạm thời comment để dùng dữ liệu mẫu
    }

    bindEvents() {
        // Apply discount code
        $(document).on('click', '#applyCodeBtn', () => this.applyDiscountCode());

        // Enter key for input
        $(document).on('keypress', '#discountCodeInput', (e) => {
            if (e.which === 13) { // Enter key
                this.applyDiscountCode();
            }
        });

        // Select discount from list
        $(document).on('click', '.select-discount-btn', (e) => {
            const code = $(e.target).attr('data-code');
            this.selectDiscountFromList(code);
        });

        // Remove applied discount
        $(document).on('click', '.btn-remove, #remove-voucher-btn', () => {
            this.removeAppliedDiscount();
        });
    }

    applyDiscountCode() {
        const code = $('#discountCodeInput').val().trim().toUpperCase();
        
        if (!code) {
            this.showResult('Vui lòng nhập mã voucher', 'error');
            return;
        }

        const discount = this.discountCodes.find(d => d.code === code);
        
        if (!discount) {
            this.showResult('Mã voucher không hợp lệ', 'error');
            return;
        }

        this.appliedDiscount = { ...discount };
        this.saveAppliedDiscount();
        this.updateAppliedDiscountDisplay();
        
        // Show success message
        $('.success-message').show().find('span').text(`Đã áp dụng mã ${code} thành công!`);
        
        $('#discountCodeInput').val('');
        
        // Close modal after short delay
        setTimeout(() => {
            $('#discountModal').modal('hide');
            this.showToast(`Đã áp dụng voucher ${code}!`, 'success');
        }, 1500);
        
        // Update cart if function exists
        if (typeof updateCartTotal === 'function') {
            updateCartTotal();
        }
    }

    selectDiscountFromList(code) {
        const discount = this.discountCodes.find(d => d.code === code);
        
        if (discount) {
            this.appliedDiscount = { ...discount };
            this.saveAppliedDiscount();
            this.updateAppliedDiscountDisplay();
            
            // Update UI
            $('.voucher-item').removeClass('selected');
            $(`[data-code="${code}"]`).addClass('selected');
            
            // Show success message
            $('#selectResult').html(`<div class="alert alert-success"><i class="fas fa-check-circle"></i> Đã chọn voucher ${code} thành công!</div>`);
            
            // Close modal after short delay
            setTimeout(() => {
                $('#voucherModal').modal('hide');
                this.showToast(`Đã áp dụng voucher ${code}!`, 'success');
            }, 1500);
            
            // Update cart if function exists
            if (typeof updateCartTotal === 'function') {
                updateCartTotal();
            }
        }
    }

    selectDiscount(code) {
        // Keep this method for backward compatibility
        this.selectDiscountFromList(code);
    }

    removeAppliedDiscount() {
        this.appliedDiscount = null;
        this.saveAppliedDiscount();
        this.updateAppliedDiscountDisplay();
        
        // Update cart if function exists
        if (typeof updateCartTotal === 'function') {
            updateCartTotal();
        }
        
        this.showToast('Đã hủy voucher', 'info');
    }

    calculateDiscount(subtotal) {
        if (!this.appliedDiscount) return 0;
        
        const discount = this.appliedDiscount;
        
        // Check minimum order
        if (subtotal < discount.minOrder) {
            return 0;
        }
        
        switch (discount.type) {
            case 'percentage':
                return Math.min(subtotal * (discount.value / 100), subtotal);
            case 'fixed':
                return Math.min(discount.value, subtotal);
            case 'freeship':
                return 0; // Handle shipping separately
            default:
                return 0;
        }
    }

    async loadVouchersFromAPI() {
        try {
            this.isLoading = true;
            this.showLoadingState();
            
            const response = await fetch('/cart/vouchers');
            const data = await response.json();
            
            if (data.success && data.vouchers) {
                this.discountCodes = data.vouchers.map(voucher => ({
                    id: voucher.id,
                    code: voucher.code,
                    type: voucher.type,
                    value: voucher.value,
                    title: voucher.title,
                    description: voucher.description,
                    condition: voucher.condition,
                    note: `HSD: ${new Date(voucher.endAt).toLocaleDateString('vi-VN')}`,
                    minOrder: voucher.minOrder,
                    maxDiscount: voucher.maxDiscount,
                    category: voucher.type === 'percentage' ? 'percent' : 'fixed',
                    icon: voucher.icon
                }));
                
                this.renderDiscountList();
            } else {
                this.showError('Không thể tải danh sách voucher');
            }
        } catch (error) {
            console.error('Error loading vouchers:', error);
            this.showError('Lỗi kết nối. Vui lòng thử lại sau.');
        } finally {
            this.isLoading = false;
            this.hideLoadingState();
        }
    }

    showLoadingState() {
        const container = document.getElementById('voucherList');
        if (container) {
            container.innerHTML = `
                <div class="voucher-loading">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Đang tải...</span>
                    </div>
                    <p class="mt-2">Đang tải danh sách voucher...</p>
                </div>
            `;
        }
    }

    hideLoadingState() {
        // Loading state will be replaced by renderDiscountList
    }

    showError(message) {
        const container = document.getElementById('voucherList');
        if (container) {
            container.innerHTML = `
                <div class="voucher-error text-center p-4">
                    <i class="fas fa-exclamation-triangle text-warning mb-2" style="font-size: 2rem;"></i>
                    <p class="mb-2">${message}</p>
                    <button class="btn btn-primary btn-sm" onclick="discountModal.loadVouchersFromAPI()">Thử lại</button>
                </div>
            `;
        }
    }

    renderDiscountList() {
        const container = document.getElementById('voucherList');
        if (!container) return;
        
        if (this.discountCodes.length === 0) {
            container.innerHTML = `
                <div class="voucher-empty text-center p-4">
                    <i class="fas fa-ticket-alt text-muted mb-2" style="font-size: 2rem;"></i>
                    <p class="text-muted">Hiện tại không có voucher nào khả dụng</p>
                </div>
            `;
            return;
        }
        
        container.innerHTML = this.discountCodes.map(discount => `
            <div class="voucher-item" data-code="${discount.code}" onclick="discountModal.selectDiscount('${discount.code}')">
                <div class="voucher-left">
                    <div class="voucher-icon">
                        ${discount.discount_type === 'percentage' ? discount.discount_value + '%<br>OFF' : 
                          discount.discount_type === 'fixed' ? '₫' + (discount.discount_value / 1000) + 'K<br>OFF' : 'FREE<br>SHIP'}
                    </div>
                    <div class="voucher-details">
                        <div class="voucher-title">${discount.title}</div>
                        <div class="voucher-desc">${discount.description}</div>
                        <div class="voucher-condition">Đơn tối thiểu: ${new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(discount.min_order_value)}</div>
                        <div class="voucher-code">Mã: ${discount.code}</div>
                    </div>
                </div>
                <div class="voucher-right">
                    <button class="btn-select">Chọn</button>
                </div>
            </div>
        `).join('');
    }

    updateAppliedDiscountDisplay() {
        const selectedVoucher = $('#selected-voucher');
        const voucherMain = $('.voucher-main');
        
        if (this.appliedDiscount) {
            // Hiển thị voucher đã chọn
            $('#selected-voucher-code').text(this.appliedDiscount.code);
            $('#selected-voucher-value').text(this.formatDiscount(this.appliedDiscount));
            selectedVoucher.show();
            voucherMain.hide();
        } else {
            // Ẩn voucher đã chọn và hiển thị nút chọn voucher
            selectedVoucher.hide();
            voucherMain.show();
        }
    }
    
    formatDiscount(discount) {
        if (discount.discount_type === 'percentage') {
            return discount.discount_value + '%';
        } else if (discount.discount_type === 'fixed') {
            return new Intl.NumberFormat('vi-VN', {
                style: 'currency',
                currency: 'VND'
            }).format(discount.discount_value);
        } else {
            // Fallback for other formats
            return discount.discount_value || discount.amount || discount.discount || '0';
        }
    }

    showResult(message, type) {
        const resultDiv = document.getElementById('voucherResult');
        if (resultDiv) {
            resultDiv.className = `result-message ${type}`;
            resultDiv.textContent = message;
            resultDiv.style.display = 'block';
            
            setTimeout(() => {
                resultDiv.style.display = 'none';
            }, 3000);
        }
    }

    showToast(message, type = 'info') {
        // Create toast element
        const toast = document.createElement('div');
        toast.className = `toast align-items-center text-white bg-${type === 'success' ? 'success' : type === 'error' ? 'danger' : 'info'} border-0`;
        toast.setAttribute('role', 'alert');
        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">${message}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;
        
        // Add to toast container
        let toastContainer = document.getElementById('toastContainer');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.id = 'toastContainer';
            toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
            document.body.appendChild(toastContainer);
        }
        
        toastContainer.appendChild(toast);
        
        // Show toast
        const bsToast = new bootstrap.Toast(toast);
        bsToast.show();
        
        // Remove after hide
        toast.addEventListener('hidden.bs.toast', () => {
            toast.remove();
        });
    }

    saveAppliedDiscount() {
        if (this.appliedDiscount) {
            localStorage.setItem('appliedDiscount', JSON.stringify(this.appliedDiscount));
        }
    }

    loadAppliedDiscount() {
        const saved = localStorage.getItem('appliedDiscount');
        if (saved) {
            try {
                this.appliedDiscount = JSON.parse(saved);
            } catch (e) {
                localStorage.removeItem('appliedDiscount');
            }
        }
    }

    getAppliedDiscount() {
        return this.appliedDiscount;
    }
}

// Initialize when DOM is loaded
$(document).ready(function() {
    window.discountModal = new DiscountModal();
});