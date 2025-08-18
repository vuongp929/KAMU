{{-- Shopee Voucher Component --}}
<div class="voucher-section">
    {{-- Hiển thị voucher đã chọn --}}
    <div id="selected-voucher" class="selected-voucher" style="display: none;">
        <div class="voucher-applied">
            <div class="voucher-applied-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="voucher-applied-content">
                <div class="voucher-applied-title">
                    <span id="selected-voucher-code"></span>
                </div>
                <div class="voucher-applied-value">
                    Tiết kiệm: <span id="selected-voucher-value"></span>
                </div>
            </div>
            <button type="button" id="remove-voucher-btn" class="voucher-remove-btn">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
    
    {{-- Nút chọn voucher --}}
    <div class="voucher-main" data-toggle="modal" data-target="#voucherModal">
        <div class="voucher-left">
            <div class="voucher-icon">
                <i class="fas fa-ticket-alt"></i>
            </div>
            <div class="voucher-content">
                <span class="voucher-title">KUMA Voucher</span>
            </div>
        </div>
        <div class="voucher-action">
            <span class="voucher-link">Chọn hoặc nhập mã</span>
        </div>
    </div>
</div>

{{-- Modal đã được chuyển sang discount-modal.blade.php --}}

<style>
/* Shopee Voucher Section Styles */
.voucher-section {
    margin-bottom: 15px;
}

.voucher-main {
    background: white;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 12px 16px;
    display: flex;
    align-items: center;
    cursor: pointer;
    transition: all 0.3s ease;
    color: #333;
    width: 100%;
    box-sizing: border-box;
}

.voucher-main:hover {
    background: #f8f9fa;
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.voucher-left {
    display: flex;
    align-items: center;
    flex: 1;
    margin-left: 0;
}

.voucher-icon {
    font-size: 20px;
    margin-right: 12px;
    color: #ee4d2d;
}

.voucher-content {
    display: flex;
    align-items: center;
    justify-content: flex-start;
    flex: 1;
    margin-left: -80px;
}

.voucher-title {
    font-size: 16px;
    font-weight: 600;
    color: #333;
    letter-spacing: 0.5px;
}

.voucher-action {
    display: flex;
    align-items: center;
    color: #333;
    gap: 6px;
}

.voucher-link {
    font-size: 13px;
    color: #4a90e2;
    font-weight: 500;
}

.selected-voucher {
    background: white;
    border-radius: 8px;
    border: 1px solid #e9ecef;
    margin-bottom: 10px;
    width: 100%;
    box-sizing: border-box;
}

.voucher-applied {
    padding: 12px 16px;
    display: flex;
    align-items: center;
    background: white;
    border-radius: 8px;
    border-left: 4px solid #52c41a;
    gap: 12px;
    width: 100%;
    box-sizing: border-box;
}

.voucher-applied-icon {
    color: #52c41a;
    font-size: 16px;
    flex-shrink: 0;
}

.voucher-applied-content {
    flex: 1;
}

.voucher-applied-title {
    font-weight: 600;
    color: #333;
    font-size: 13px;
    margin-bottom: 2px;
}

.voucher-applied-value {
    font-weight: 600;
    color: #ee4d2d;
    font-size: 12px;
}

.voucher-remove-btn {
    background: none;
    border: none;
    color: #999;
    font-size: 14px;
    cursor: pointer;
    padding: 4px;
    border-radius: 4px;
    transition: all 0.2s ease;
    flex-shrink: 0;
}

.voucher-remove-btn:hover {
    background: #f5f5f5;
    color: #666;
}

/* Modal Styles */
.voucher-modal .modal-content {
    border: none;
    border-radius: 12px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
}

.voucher-modal .modal-header {
    background: linear-gradient(135deg, #ee4d2d 0%, #ff6533 100%);
    color: white;
    border-bottom: none;
    border-radius: 12px 12px 0 0;
}

.voucher-modal .btn-close {
    filter: brightness(0) invert(1);
}

.voucher-tabs {
    border-bottom: 2px solid #f0f0f0;
    margin-bottom: 20px;
}

.voucher-tabs .nav-link {
    border: none;
    color: #666;
    font-weight: 600;
    padding: 12px 24px;
    border-radius: 0;
}

.voucher-tabs .nav-link.active {
    color: #ee4d2d;
    border-bottom: 2px solid #ee4d2d;
    background: none;
}

.enter-code-section {
    padding: 20px 0;
}

.voucher-input-group {
    margin-bottom: 16px;
}

.voucher-input-group .form-control {
    border: 2px solid #e5e5e5;
    border-right: none;
    border-radius: 8px 0 0 8px;
    padding: 12px 16px;
    font-size: 14px;
}

.voucher-input-group .form-control:focus {
    border-color: #ee4d2d;
    box-shadow: none;
}

.btn-apply {
    background: #ee4d2d;
    border: 2px solid #ee4d2d;
    color: white;
    border-radius: 0 8px 8px 0;
    padding: 12px 24px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-apply:hover {
    background: #d73527;
    border-color: #d73527;
    transform: translateY(-1px);
}

.voucher-message {
    font-size: 13px;
    margin-top: 8px;
}

.voucher-message.success {
    color: #52c41a;
}

.voucher-message.error {
    color: #ff4d4f;
}

.voucher-list {
    max-height: 400px;
    overflow-y: auto;
}

.voucher-item {
    background: white;
    border: 2px solid #e5e5e5;
    border-radius: 8px;
    padding: 16px;
    margin-bottom: 12px;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
}

.voucher-item:hover {
    border-color: #ee4d2d;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(238, 77, 45, 0.1);
}

.voucher-item.selected {
    border-color: #52c41a;
    background: #f6ffed;
}

.voucher-item-icon {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, #ee4d2d 0%, #ff6533 100%);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 18px;
    margin-right: 16px;
}

.voucher-item-content {
    flex: 1;
}

.voucher-item-title {
    font-weight: 600;
    color: #333;
    font-size: 14px;
    margin-bottom: 4px;
}

.voucher-item-desc {
    color: #666;
    font-size: 13px;
    margin-bottom: 8px;
}

.voucher-item-condition {
    color: #999;
    font-size: 12px;
}

.voucher-item-value {
    color: #ee4d2d;
    font-weight: 600;
    font-size: 16px;
    text-align: right;
}

/* Responsive */
@media (max-width: 768px) {
    .voucher-modal .modal-dialog {
        margin: 0;
        max-width: 100%;
        height: 100vh;
    }
    
    .voucher-modal .modal-content {
        height: 100vh;
        border-radius: 0;
    }
    
    .voucher-header {
        padding: 12px 16px;
    }
    
    .voucher-title {
        font-size: 14px;
    }
    
    .voucher-select-btn {
        padding: 6px 12px;
        font-size: 13px;
    }
}
</style>