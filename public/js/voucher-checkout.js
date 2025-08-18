// Voucher Checkout JavaScript
class VoucherCheckout {
    constructor() {
        this.selectedVoucher = null;
        this.voucherData = [];
        this.isLoading = false;
        
        this.init();
    }

    switchTab(tabName) {
        // Remove active class from all tabs
        document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
        document.querySelector(`[data-tab="${tabName}"]`).classList.add('active');

        // Hide all panels
        document.querySelectorAll('.tab-panel').forEach(panel => panel.classList.remove('active'));
        document.getElementById(tabName).classList.add('active');
    }

    init() {
        this.bindEvents();
        this.loadSavedVoucher();
        this.loadVouchersFromAPI();
    }

    bindEvents() {
        // Áp dụng voucher từ input
        document.getElementById('applyCodeBtn')?.addEventListener('click', () => {
            this.applyVoucherFromInput();
        });

        // Enter key trong input
        document.getElementById('discountCodeInput')?.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                this.applyVoucherFromInput();
            }
        });

        // Xóa voucher đã chọn
        document.getElementById('remove-voucher-btn')?.addEventListener('click', () => {
            this.removeVoucher();
        });

        // Đóng modal khi chọn voucher
        document.getElementById('voucherModal')?.addEventListener('hidden.bs.modal', () => {
            this.clearMessage();
        });

        // Tab switching
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('tab-btn')) {
                e.preventDefault();
                const tabName = e.target.getAttribute('data-tab');
                this.switchTab(tabName);
            }
        });

        // Select voucher from list
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('select-discount-btn')) {
                const code = e.target.getAttribute('data-code');
                this.selectVoucher(code);
            }
        });

        // Bind sự kiện cho form submit để xóa voucher sau khi đặt hàng
        const checkoutForm = document.querySelector('form');
        if (checkoutForm) {
            checkoutForm.addEventListener('submit', () => this.clearVoucherAfterOrder());
        }
    }

    async loadVouchersFromAPI() {
        try {
            this.isLoading = true;
            this.showLoadingState();
            
            const response = await fetch('/cart/vouchers');
            const data = await response.json();
            
            if (data.success && data.vouchers) {
                this.voucherData = data.vouchers.map(voucher => ({
                    id: voucher.id,
                    code: voucher.code,
                    title: voucher.title,
                    description: voucher.description,
                    type: voucher.type === 'percentage' ? 'percentage' : 'fixed',
                    value: voucher.value,
                    maxDiscount: voucher.maxDiscount,
                    minOrder: voucher.minOrder,
                    condition: voucher.condition,
                    icon: voucher.icon || 'fas fa-ticket-alt'
                }));
                
                this.renderVoucherList();
            } else {
                this.showError('Không thể tải danh sách voucher');
            }
        } catch (error) {
            console.error('Error loading vouchers:', error);
            this.showError('Lỗi kết nối. Vui lòng thử lại sau.');
        } finally {
            this.isLoading = false;
        }
    }

    showLoadingState() {
        const voucherList = document.getElementById('voucherList');
        if (voucherList) {
            voucherList.innerHTML = `
                <div class="voucher-loading text-center p-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Đang tải...</span>
                    </div>
                    <p class="mt-2">Đang tải danh sách voucher...</p>
                </div>
            `;
        }
    }

    showError(message) {
        const voucherList = document.getElementById('voucherList');
        if (voucherList) {
            voucherList.innerHTML = `
                <div class="voucher-error text-center p-4">
                    <i class="fas fa-exclamation-triangle text-warning mb-2" style="font-size: 2rem;"></i>
                    <p class="mb-2">${message}</p>
                    <button class="btn btn-primary btn-sm" onclick="voucherCheckout.loadVouchersFromAPI()">Thử lại</button>
                </div>
            `;
        }
    }

    renderVoucherList() {
        const voucherList = document.getElementById('voucherList');
        if (!voucherList) return;

        if (this.voucherData.length === 0) {
            voucherList.innerHTML = `
                <div class="voucher-empty text-center p-4">
                    <i class="fas fa-ticket-alt text-muted mb-2" style="font-size: 2rem;"></i>
                    <p class="text-muted">Hiện tại không có voucher nào khả dụng</p>
                </div>
            `;
            return;
        }

        voucherList.innerHTML = this.voucherData.map(voucher => `
            <div class="voucher-item" data-code="${voucher.code}" onclick="voucherCheckout.selectVoucher('${voucher.code}')">
                <div class="voucher-item-icon">
                    <i class="${voucher.icon}"></i>
                </div>
                <div class="voucher-item-content">
                    <div class="voucher-item-title">${voucher.title}</div>
                    <div class="voucher-item-desc">${voucher.description}</div>
                    <div class="voucher-item-condition">${voucher.condition}</div>
                </div>
                <div class="voucher-item-value">
                    ${this.formatVoucherValue(voucher)}
                </div>
            </div>
        `).join('');
    }

    formatVoucherValue(voucher) {
        switch (voucher.type) {
            case 'percentage':
                return `-${voucher.value}%`;
            case 'fixed':
                return `-${this.formatCurrency(voucher.value)}`;
            case 'free_shipping':
                return 'Miễn phí ship';
            default:
                return '';
        }
    }

    formatCurrency(amount) {
        return new Intl.NumberFormat('vi-VN', {
            style: 'currency',
            currency: 'VND'
        }).format(amount).replace('₫', 'đ');
    }

    applyVoucherFromInput() {
        const input = document.getElementById('discountCodeInput');
        const code = input.value.trim().toUpperCase();
        
        if (!code) {
            this.showMessage('Vui lòng nhập mã voucher', 'error');
            return;
        }

        const voucher = this.voucherData.find(v => v.code === code);
        if (!voucher) {
            this.showMessage('Mã voucher không hợp lệ', 'error');
            return;
        }

        this.applyVoucher(voucher);
    }

    selectVoucher(code) {
        const voucher = this.voucherData.find(v => v.code === code);
        if (voucher) {
            this.applyVoucher(voucher);
        }
    }

    applyVoucher(voucher) {
        const orderTotal = this.getOrderTotal();
        
        // Kiểm tra điều kiện đơn hàng tối thiểu
        if (voucher.minOrder && orderTotal < voucher.minOrder) {
            this.showMessage(`Đơn hàng tối thiểu ${this.formatCurrency(voucher.minOrder)} để sử dụng voucher này`, 'error');
            return;
        }

        // Tính toán giá trị giảm giá
        const discountAmount = this.calculateDiscount(voucher, orderTotal);
        
        // Lưu voucher đã chọn
        this.selectedVoucher = {
            ...voucher,
            discountAmount
        };

        // Hiển thị voucher đã chọn
        this.displaySelectedVoucher();
        
        // Cập nhật tổng tiền
        this.updateOrderTotal();
        
        // Lưu vào localStorage
        this.saveVoucher();
        
        // Đóng modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('voucherModal'));
        if (modal) {
            modal.hide();
        }
        
        // Hiển thị thông báo thành công
        this.showToast(`Áp dụng voucher ${voucher.code} thành công!`, 'success');
    }

    calculateDiscount(voucher, orderTotal) {
        switch (voucher.type) {
            case 'percentage':
                const percentDiscount = (orderTotal * voucher.value) / 100;
                return voucher.maxDiscount ? Math.min(percentDiscount, voucher.maxDiscount) : percentDiscount;
            case 'fixed':
                return Math.min(voucher.value, orderTotal);
            case 'free_shipping':
                return voucher.value;
            default:
                return 0;
        }
    }

    displaySelectedVoucher() {
        if (!this.selectedVoucher) return;

        const selectedVoucherDiv = document.getElementById('selected-voucher');
        const codeSpan = document.getElementById('selected-voucher-code');
        const descSpan = document.getElementById('selected-voucher-desc');
        const valueSpan = document.getElementById('selected-voucher-value');

        if (selectedVoucherDiv && codeSpan && valueSpan) {
            codeSpan.textContent = this.selectedVoucher.code;
            if (descSpan) {
                descSpan.textContent = this.selectedVoucher.description || 'Voucher giảm giá';
            }
            valueSpan.textContent = this.formatCurrency(this.selectedVoucher.discountAmount);
            selectedVoucherDiv.style.display = 'block';
        }

        // Cập nhật hidden inputs cho form
        this.updateHiddenInputs();
    }

    updateHiddenInputs() {
        const discountCodeInput = document.getElementById('discount-code-hidden');
        const discountValueInput = document.getElementById('discount-value-hidden');
        const finalTotalInput = document.getElementById('final-total-hidden');

        console.log('Updating hidden inputs:', {
            selectedVoucher: this.selectedVoucher,
            discountCodeInput: discountCodeInput,
            discountValueInput: discountValueInput,
            finalTotalInput: finalTotalInput
        });

        if (this.selectedVoucher) {
            if (discountCodeInput) {
                discountCodeInput.value = this.selectedVoucher.code;
                console.log('Set discount code:', this.selectedVoucher.code);
            }
            if (discountValueInput) {
                discountValueInput.value = this.selectedVoucher.discountAmount;
                console.log('Set discount value:', this.selectedVoucher.discountAmount);
            }
            if (finalTotalInput) {
                const finalTotal = this.getOrderTotal() - this.selectedVoucher.discountAmount;
                finalTotalInput.value = finalTotal;
                console.log('Set final total:', finalTotal);
            }
        } else {
            if (discountCodeInput) discountCodeInput.value = '';
            if (discountValueInput) discountValueInput.value = '0';
            if (finalTotalInput) finalTotalInput.value = this.getOrderTotal();
        }
    }

    removeVoucher() {
        this.selectedVoucher = null;
        
        // Ẩn voucher đã chọn
        const selectedVoucherDiv = document.getElementById('selected-voucher');
        if (selectedVoucherDiv) {
            selectedVoucherDiv.style.display = 'none';
        }
        
        // Cập nhật tổng tiền
        this.updateOrderTotal();
        
        // Xóa khỏi localStorage
        localStorage.removeItem('selectedVoucher');
        
        // Cập nhật hidden inputs
        this.updateHiddenInputs();
        
        // Hiển thị thông báo
        this.showToast('Đã hủy voucher', 'info');
    }

    updateOrderTotal() {
        const orderTotal = this.getOrderTotal();
        const discount = this.selectedVoucher ? this.selectedVoucher.discountAmount : 0;
        const finalTotal = orderTotal - discount;

        // Cập nhật hiển thị tổng tiền
        const totalElement = document.getElementById('total-amount');
        if (totalElement) {
            totalElement.textContent = this.formatCurrency(finalTotal);
        }

        // Cập nhật hiển thị giảm giá
        const discountElement = document.getElementById('discount-amount');
        const discountRow = document.getElementById('discount-row');
        if (discountElement && discountRow) {
            discountElement.textContent = discount > 0 ? `-${this.formatCurrency(discount)}` : '-0đ';
            discountRow.style.display = discount > 0 ? 'flex' : 'none';
        }
    }

    getOrderTotal() {
        // Lấy tổng tiền ban đầu từ data-order-total attribute
        const finalTotalInput = document.getElementById('final-total-hidden');
        if (finalTotalInput && finalTotalInput.dataset.orderTotal) {
            return parseInt(finalTotalInput.dataset.orderTotal) || 0;
        }
        
        // Fallback: lấy từ hidden input value
        if (finalTotalInput) {
            return parseInt(finalTotalInput.value) || 0;
        }
        
        return 0;
    }

    saveVoucher() {
        if (this.selectedVoucher) {
            localStorage.setItem('selectedVoucher', JSON.stringify(this.selectedVoucher));
        }
    }

    loadSavedVoucher() {
        const saved = localStorage.getItem('selectedVoucher');
        if (saved) {
            try {
                this.selectedVoucher = JSON.parse(saved);
                this.displaySelectedVoucher();
                this.updateOrderTotal();
            } catch (e) {
                localStorage.removeItem('selectedVoucher');
            }
        }
    }

    showMessage(message, type = 'info') {
        const messageDiv = document.getElementById('codeResult');
        if (messageDiv) {
            messageDiv.textContent = message;
            messageDiv.className = `result-message ${type}`;
        }
    }

    clearMessage() {
        const messageDiv = document.getElementById('codeResult');
        if (messageDiv) {
            messageDiv.textContent = '';
            messageDiv.className = 'result-message';
        }
    }

    showToast(message, type = 'info') {
        // Tạo toast notification
        const toast = document.createElement('div');
        toast.className = `toast-notification toast-${type}`;
        toast.innerHTML = `
            <div class="toast-content">
                <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
                <span>${message}</span>
            </div>
        `;
        
        // Thêm styles cho toast
        if (!document.getElementById('toast-styles')) {
            const styles = document.createElement('style');
            styles.id = 'toast-styles';
            styles.textContent = `
                .toast-notification {
                    position: fixed;
                    top: 20px;
                    right: 20px;
                    background: white;
                    border-radius: 8px;
                    padding: 16px 20px;
                    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
                    z-index: 9999;
                    transform: translateX(100%);
                    transition: transform 0.3s ease;
                    border-left: 4px solid #ccc;
                }
                .toast-notification.toast-success { border-left-color: #52c41a; }
                .toast-notification.toast-error { border-left-color: #ff4d4f; }
                .toast-notification.toast-info { border-left-color: #1890ff; }
                .toast-notification.show { transform: translateX(0); }
                .toast-content {
                    display: flex;
                    align-items: center;
                    gap: 8px;
                    font-size: 14px;
                }
                .toast-content i {
                    font-size: 16px;
                }
                .toast-success .toast-content i { color: #52c41a; }
                .toast-error .toast-content i { color: #ff4d4f; }
                .toast-info .toast-content i { color: #1890ff; }
            `;
            document.head.appendChild(styles);
        }
        
        document.body.appendChild(toast);
        
        // Hiển thị toast
        setTimeout(() => toast.classList.add('show'), 100);
        
        // Tự động ẩn sau 3 giây
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }

    clearVoucherAfterOrder() {
        // Xóa voucher khỏi localStorage sau khi đặt hàng thành công
        localStorage.removeItem('selectedVoucher');
        this.selectedVoucher = null;
        console.log('Voucher cleared after order');
    }
}

// Khởi tạo khi DOM ready
let voucherCheckout;
document.addEventListener('DOMContentLoaded', function() {
    voucherCheckout = new VoucherCheckout();
    
    // Debug form submission và clear voucher
    const checkoutForm = document.getElementById('checkout-form');
    if (checkoutForm) {
        checkoutForm.addEventListener('submit', function(e) {
            console.log('Form submitting...');
            const formData = new FormData(checkoutForm);
            console.log('Form data:');
            for (let [key, value] of formData.entries()) {
                console.log(key + ': ' + value);
            }
            
            // Clear voucher ngay khi submit form
            setTimeout(() => {
                if (voucherCheckout) {
                    voucherCheckout.clearVoucherAfterOrder();
                }
            }, 100);
        });
    }
});