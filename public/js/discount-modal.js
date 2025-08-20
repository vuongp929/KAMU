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
    }

    resetModal() {
        $('#voucherCode').val('');
        $('.voucher-card').removeClass('selected');
        this.selectedDiscount = null;
        this.hideConfirmButton();
    }

    bindEvents() {
        const self = this;
        
        // Apply discount code button
        $(document).on('click', '#applyDiscountBtn', function() {
            self.applyDiscountCode();
        });

        // Select discount from list
        $(document).on('click', '.select-discount-btn', function(e) {
            e.stopPropagation();
            const code = $(this).data('code');
            self.selectDiscountFromList(code);
        });

        // Modal events
        $('#voucherModal').on('shown.bs.modal', function() {
            self.loadVouchers();
        });

        $('#voucherModal').on('hidden.bs.modal', function() {
            self.resetModal();
        });

        // Remove applied discount
        $(document).on('click', '.btn-remove, #remove-voucher-btn', function() {
            self.removeAppliedDiscount();
        });

        // Confirm voucher selection
        $(document).on('click', '#confirmVoucherBtn', function(e) {
            e.preventDefault();
            self.confirmVoucherSelection();
        });
    }

    applyDiscountCode() {
        const code = $('#voucherCode').val().trim().toUpperCase();
        if (!code) {
            this.showResult('Vui lòng nhập mã giảm giá', 'error');
            return;
        }

        const discount = this.discountCodes.find(d => d.code === code);
        if (discount) {
            this.selectDiscountFromList(code);
        } else {
            this.showResult('Mã giảm giá không hợp lệ', 'error');
        }
    }

    selectDiscountFromList(code) {
        const discount = this.discountCodes.find(d => d.code === code);
        if (!discount) {
            this.showResult('Mã giảm giá không tồn tại', 'error');
            return;
        }

        // Remove previous selection
        $('.voucher-card').removeClass('selected');
        
        // Add selection to current card
        $(`.voucher-card[data-code="${code}"]`).addClass('selected');
        
        // Store selected discount
        this.selectedDiscount = discount;
        
        // Show confirm button instead of auto-closing
        this.showConfirmButton();
        
        this.showResult(`Đã chọn voucher ${discount.title}`, 'success');
    }

    selectDiscount(code) {
        this.selectDiscountFromList(code);
    }

    removeAppliedDiscount() {
        this.appliedDiscount = null;
        this.selectedDiscount = null;
        localStorage.removeItem('appliedDiscount');
        this.updateAppliedDiscountDisplay();
        this.showResult('Đã hủy áp dụng mã giảm giá', 'info');
    }

    calculateDiscount(subtotal) {
        if (!this.appliedDiscount) return 0;
        
        if (this.appliedDiscount.discount_type === 'percentage') {
            return Math.min(subtotal * this.appliedDiscount.discount_value / 100, this.appliedDiscount.max_discount || subtotal);
        } else {
            return Math.min(this.appliedDiscount.discount_value, subtotal);
        }
    }

    renderDiscountList() {
        const container = $('#voucher-list');
        if (!container.length) return;
        
        container.html(this.discountCodes.map(discount => {
            const iconClass = this.getVoucherIconClass(discount.discount_type);
            const iconText = this.getVoucherIconText(discount);
            const discountText = discount.discount_type === 'percentage' 
                ? `Giảm ${discount.discount_value}%` 
                : `Giảm ${this.formatCurrency(discount.discount_value)}`;
            
            return `
                <div class="voucher-card" data-code="${discount.code}" onclick="discountModal.selectDiscount('${discount.code}')">
                    <div class="voucher-icon ${iconClass}">
                        <div class="icon-text">${iconText}</div>
                        <div class="icon-subtitle">TOÀN NGÀNH HÀNG</div>
                    </div>
                    <div class="voucher-content">
                        <div class="voucher-title">${discountText}</div>
                        <div class="voucher-subtitle">Đơn Tối Thiểu ${this.formatCurrency(discount.min_order_value || 0)}</div>
                        <div class="voucher-expiry">Sắp hết hạn. Còn 1 ngày. Điều Kiện</div>
                    </div>
                    <div class="voucher-action">
                        <button class="btn-apply select-discount-btn" data-code="${discount.code}">
                            ÁP DỤNG
                        </button>
                    </div>
                </div>
            `;
        }).join(''));
    }

    formatCurrency(amount) {
        return new Intl.NumberFormat('vi-VN', {
            style: 'currency',
            currency: 'VND'
        }).format(amount);
    }

    getVoucherIconClass(discountType) {
        switch(discountType) {
            case 'percentage':
                return 'voucher-percentage';
            case 'fixed':
                return 'voucher-fixed';
            case 'shipping':
                return 'voucher-shipping';
            default:
                return 'voucher-default';
        }
    }

    getVoucherIconText(voucher) {
        if (voucher.discount_type === 'percentage') {
            return `${voucher.discount_value}%`;
        } else if (voucher.discount_type === 'fixed') {
            if (voucher.discount_value >= 1000) {
                return `${voucher.discount_value / 1000}K`;
            } else {
                return `${voucher.discount_value}`;
            }
        } else {
            return 'FREE';
        }
    }

    showResult(message, type) {
        const alertClass = type === 'error' ? 'alert-danger' : 
                          type === 'success' ? 'alert-success' : 'alert-info';
        
        const alertHtml = `
            <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>
        `;
        
        $('#discount-result').html(alertHtml);
        
        setTimeout(() => {
            $('.alert').fadeOut();
        }, 3000);
    }

    saveAppliedDiscount() {
        if (this.appliedDiscount) {
            localStorage.setItem('appliedDiscount', JSON.stringify(this.appliedDiscount));
        }
    }

    loadAppliedDiscount() {
        const saved = localStorage.getItem('appliedDiscount');
        if (saved) {
            this.appliedDiscount = JSON.parse(saved);
        }
    }

    getAppliedDiscount() {
        return this.appliedDiscount;
    }

    showConfirmButton() {
        $('#confirmVoucherBtn').show();
    }

    hideConfirmButton() {
        $('#confirmVoucherBtn').hide();
    }

    confirmVoucherSelection() {
        if (this.selectedDiscount) {
            // Apply the selected discount
            this.appliedDiscount = this.selectedDiscount;
            this.saveAppliedDiscount();
            
            // Close modal
            $('#voucherModal').modal('hide');
            
            // Update cart if function exists
            if (typeof updateCartTotal === 'function') {
                updateCartTotal();
            }
            
            // Show success message
            this.showResult(`Đã áp dụng voucher ${this.appliedDiscount.title}`, 'success');
        }
    }

    loadVouchers() {
        // For now, just render the static list
        this.renderDiscountList();
    }

    updateAppliedDiscountDisplay() {
        // This function can be used to update UI when discount is applied
        // For now, it's just a placeholder
    }
}

// Initialize when DOM is loaded
$(document).ready(function() {
    window.discountModal = new DiscountModal();
});