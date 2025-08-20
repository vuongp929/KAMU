// Checkout Form Persistence JavaScript
class CheckoutFormPersistence {
    constructor() {
        this.storageKey = 'checkoutFormData';
        this.init();
    }

    init() {
        this.bindEvents();
        this.loadSavedData();
    }

    bindEvents() {
        // Lưu dữ liệu khi người dùng nhập
        const formFields = ['name', 'email', 'phone', 'address'];
        
        formFields.forEach(fieldName => {
            const field = document.querySelector(`input[name="${fieldName}"]`);
            if (field) {
                field.addEventListener('input', () => {
                    this.saveFormData();
                });
                field.addEventListener('blur', () => {
                    this.saveFormData();
                });
            }
        });

        // Lưu dữ liệu khi chọn phương thức thanh toán
        const paymentMethods = document.querySelectorAll('input[name="payment_method"]');
        paymentMethods.forEach(method => {
            method.addEventListener('change', () => {
                this.saveFormData();
            });
        });

        // Xóa dữ liệu khi đặt hàng thành công
        const checkoutForm = document.querySelector('#checkout-form');
        if (checkoutForm) {
            checkoutForm.addEventListener('submit', (e) => {
                // Chỉ xóa dữ liệu nếu form được submit thành công
                // Sẽ được xóa sau khi redirect thành công
                setTimeout(() => {
                    this.clearSavedData();
                }, 1000);
            });
        }
    }

    saveFormData() {
        const formData = {
            name: this.getFieldValue('name'),
            email: this.getFieldValue('email'),
            phone: this.getFieldValue('phone'),
            address: this.getFieldValue('address'),
            payment_method: this.getSelectedPaymentMethod(),
            timestamp: Date.now()
        };

        // Chỉ lưu nếu có ít nhất một field có dữ liệu
        if (Object.values(formData).some(value => value && value.toString().trim() !== '')) {
            localStorage.setItem(this.storageKey, JSON.stringify(formData));
            console.log('Form data saved:', formData);
        }
    }

    loadSavedData() {
        try {
            const savedData = localStorage.getItem(this.storageKey);
            if (savedData) {
                const formData = JSON.parse(savedData);
                
                // Kiểm tra xem dữ liệu có quá cũ không (24 giờ)
                const now = Date.now();
                const dataAge = now - (formData.timestamp || 0);
                const maxAge = 24 * 60 * 60 * 1000; // 24 giờ
                
                if (dataAge > maxAge) {
                    this.clearSavedData();
                    return;
                }

                // Khôi phục dữ liệu vào form
                this.restoreFormData(formData);
                console.log('Form data restored:', formData);
            }
        } catch (error) {
            console.error('Error loading saved form data:', error);
            this.clearSavedData();
        }
    }

    restoreFormData(formData) {
        // Khôi phục các field text
        ['name', 'email', 'phone', 'address'].forEach(fieldName => {
            if (formData[fieldName]) {
                const field = document.querySelector(`input[name="${fieldName}"]`);
                if (field && !field.value) { // Chỉ khôi phục nếu field đang trống
                    field.value = formData[fieldName];
                }
            }
        });

        // Khôi phục phương thức thanh toán
        if (formData.payment_method) {
            const paymentMethod = document.querySelector(`input[name="payment_method"][value="${formData.payment_method}"]`);
            if (paymentMethod) {
                paymentMethod.checked = true;
            }
        }
    }

    getFieldValue(fieldName) {
        const field = document.querySelector(`input[name="${fieldName}"]`);
        return field ? field.value.trim() : '';
    }

    getSelectedPaymentMethod() {
        const selectedMethod = document.querySelector('input[name="payment_method"]:checked');
        return selectedMethod ? selectedMethod.value : '';
    }

    clearSavedData() {
        localStorage.removeItem(this.storageKey);
        console.log('Checkout form data cleared');
    }

    // Method để xóa dữ liệu khi đặt hàng thành công
    clearOnSuccess() {
        this.clearSavedData();
    }
}

// Khởi tạo khi DOM ready
let checkoutFormPersistence;
document.addEventListener('DOMContentLoaded', function() {
    checkoutFormPersistence = new CheckoutFormPersistence();
});

// Export để có thể sử dụng từ bên ngoài
if (typeof window !== 'undefined') {
    window.checkoutFormPersistence = checkoutFormPersistence;
}