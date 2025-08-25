{{-- Shopee Style Voucher Modal --}}
<div class="modal fade" id="voucherModal" tabindex="-1" role="dialog" aria-labelledby="voucherModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content shopee-modal">
            <div class="modal-header shopee-header">
                <h4 class="modal-title" id="voucherModalLabel">
                    Chọn Voucher
                </h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body shopee-body">
                {{-- Voucher Limit Notice --}}
                <div id="voucher-limit-notice" class="voucher-notice" style="display: none;">
                    <i class="fa fa-info-circle"></i>
                    <span>Mỗi đơn hàng chỉ áp dụng được 1 voucher. Muốn áp dụng voucher khác, vui lòng hủy voucher đang dùng hoặc tạo đơn hàng mới.</span>
                </div>

                {{-- Voucher Input Section --}}
                <div class="voucher-input-section">
                    <h6 class="section-title">Nhập mã voucher</h6>
                    <div class="input-group">
                        <input type="text" class="form-control voucher-input" id="discountCodeInput" 
                               placeholder="Nhập mã giảm giá" maxlength="20">
                        <div class="input-group-append">
                            <button class="btn btn-apply" type="button" id="applyCodeBtn">
                                ÁP DỤNG
                            </button>
                        </div>
                    </div>
                    <div id="codeResult" class="result-message mt-2"></div>
                </div>

                {{-- Divider --}}
                <div class="voucher-divider">
                    <span>HOẶC</span>
                </div>

                {{-- Voucher Selection Section --}}
                <div class="voucher-selection-section">
                    <h6 class="section-title">Chọn voucher có sẵn</h6>
                    
                    <div id="loading" class="loading-state" style="display: none;">
                        <div class="text-center p-4">
                            <i class="fa fa-spinner fa-spin mb-2" style="font-size: 2rem;"></i>
                            <p class="text-muted">Đang tải voucher...</p>
                        </div>
                    </div>
                    
                    <div id="error" class="error-state" style="display: none;">
                        <div class="alert alert-danger">
                            <i class="fa fa-exclamation-triangle"></i>
                            <span id="error-message">Có lỗi xảy ra khi tải voucher</span>
                        </div>
                    </div>
                    
                    <div id="voucherList" class="voucher-list">
                        {{-- Voucher items will be loaded here --}}
                    </div>
                    
                    <div id="no-vouchers" class="no-vouchers" style="display: none;">
                        <div class="text-center p-4">
                            <i class="fa fa-ticket text-muted mb-2" style="font-size: 2rem;"></i>
                            <p class="text-muted">Không có voucher khả dụng</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer shopee-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-confirm" id="confirmVoucherBtn" style="display: none;">
                    Xác nhận
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    /* Shopee Style Modal */
    .shopee-modal {
        border-radius: 8px;
        border: none;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        font-family: 'Helvetica Neue', Arial, sans-serif;
    }

    .shopee-header {
        background: #ee4d2d;
        color: white;
        border-bottom: none;
        border-radius: 8px 8px 0 0;
        padding: 16px 24px;
        position: relative;
    }

    .shopee-header .modal-title {
        font-size: 18px;
        font-weight: 600;
        margin: 0;
    }

    .shopee-header .close {
        color: white;
        opacity: 0.8;
        font-size: 24px;
        font-weight: 300;
        text-shadow: none;
    }

    .shopee-header .close:hover {
        opacity: 1;
    }

    .shopee-body {
        padding: 20px 24px;
        background: #fafafa;
        max-height: 500px;
        overflow-y: auto;
    }

    .shopee-footer {
        background: white;
        border-top: 1px solid #e5e5e5;
        padding: 16px 24px;
        border-radius: 0 0 8px 8px;
        display: flex;
        justify-content: flex-end;
        gap: 12px;
    }

    .btn-confirm {
        background: #ee4d2d;
        color: white;
        border: 1px solid #ee4d2d;
        padding: 10px 24px;
        border-radius: 4px;
        font-weight: 600;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .btn-confirm:hover {
        background: #d73527;
        border-color: #d73527;
    }

    .btn-secondary {
        background: #f5f5f5;
        color: #666;
        border: 1px solid #d9d9d9;
        padding: 10px 24px;
        border-radius: 4px;
        font-weight: 500;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .btn-secondary:hover {
        background: #e6e6e6;
        border-color: #bfbfbf;
    }

    /* Voucher Notice */
    .voucher-notice {
        background: #fff7e6;
        border: 1px solid #ffd591;
        border-radius: 6px;
        padding: 12px 16px;
        margin-bottom: 20px;
        display: flex;
        align-items: flex-start;
        gap: 8px;
        font-size: 13px;
        color: #d46b08;
    }

    .voucher-notice i {
        margin-top: 1px;
        flex-shrink: 0;
    }

    .voucher-notice span {
        line-height: 1.4;
    }

    /* Section Styles */
    .voucher-input-section {
        background: white;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .voucher-selection-section {
        background: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .section-title {
        font-size: 16px;
        font-weight: 600;
        color: #333;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
    }

    .section-title:before {
        content: '';
        width: 3px;
        height: 12px;
        background: #ee4d2d;
        margin-right: 6px;
        border-radius: 1px;
    }

    /* Divider */
    .voucher-divider {
        text-align: center;
        margin: 20px 0;
        position: relative;
    }

    .voucher-divider:before {
        content: '';
        position: absolute;
        top: 50%;
        left: 0;
        right: 0;
        height: 1px;
        background: #e5e5e5;
    }

    .voucher-divider span {
        background: #fafafa;
        padding: 0 15px;
        color: #999;
        font-size: 12px;
        font-weight: 500;
        position: relative;
        z-index: 1;
    }

    /* Input Group Styles */
    .input-group {
        display: flex;
        width: 100%;
        max-width: 100%;
        overflow: hidden;
    }

    .voucher-input {
        flex: 1;
        min-width: 0;
        padding: 12px 16px;
        border: 2px solid #e5e5e5;
        border-radius: 6px 0 0 6px;
        border-right: none;
        font-size: 14px;
        outline: none;
        transition: border-color 0.3s ease;
    }

    .voucher-input:focus {
        border-color: #ee4d2d;
        box-shadow: none;
    }

    .input-group-append {
        display: flex;
        flex-shrink: 0;
    }

    .btn-apply {
        background: #ee4d2d;
        color: white;
        border: 2px solid #ee4d2d;
        padding: 12px 18px;
        font-weight: 600;
        font-size: 14px;
        border-radius: 0 6px 6px 0;
        cursor: pointer;
        transition: all 0.3s ease;
        white-space: nowrap;
        min-width: 100px;
        flex-shrink: 0;
    }

    .btn-apply:hover {
        background: #d73527;
        border-color: #d73527;
    }

    .result-message {
        margin-top: 10px;
        padding: 8px 12px;
        border-radius: 4px;
        font-size: 13px;
        display: none;
    }

    .result-message.success {
        background: #f6ffed;
        border: 1px solid #b7eb8f;
        color: #52c41a;
        display: block;
    }

    .result-message.error {
        background: #fff2f0;
        border: 1px solid #ffccc7;
        color: #ff4d4f;
        display: block;
    }

    /* Voucher List Section */
    .voucher-filter {
        padding: 15px 0;
        border-bottom: 1px solid #f0f0f0;
        margin-bottom: 15px;
    }

    .filter-title {
        font-weight: 600;
        font-size: 16px;
        color: #333;
        display: block;
        margin-bottom: 5px;
    }

    .filter-count {
        font-size: 14px;
        color: #666;
    }

    .voucher-list {
        max-height: 400px;
        overflow-y: auto;
    }

    /* Shopee Voucher Item */
    .voucher-item {
        display: flex;
        align-items: stretch;
        border: 1px solid #e5e5e5;
        border-radius: 8px;
        margin-bottom: 12px;
        overflow: hidden;
        cursor: pointer;
        transition: all 0.3s ease;
        background: white;
    }

    .voucher-item:hover {
        border-color: #ee4d2d;
        box-shadow: 0 2px 8px rgba(238, 77, 45, 0.1);
    }

    .voucher-left {
        display: flex;
        flex: 1;
        padding: 15px;
        align-items: center;
    }

    .voucher-icon {
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        border-radius: 8px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
        font-size: 12px;
        text-align: center;
        margin-right: 15px;
        position: relative;
    }

    .voucher-icon.percent {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .voucher-icon.fixed {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    }

    .voucher-icon.freeship {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    }

    .voucher-details {
        flex: 1;
    }

    .voucher-title {
        font-weight: 600;
        font-size: 14px;
        color: #333;
        margin-bottom: 4px;
    }

    .voucher-desc {
        font-size: 12px;
        color: #666;
        margin-bottom: 4px;
    }

    .voucher-condition {
        font-size: 12px;
        color: #999;
        line-height: 1.4;
        margin-bottom: 4px;
    }

    .voucher-note {
        font-size: 11px;
        color: #ff6b35;
        background: #fff5f0;
        padding: 4px 8px;
        border-radius: 4px;
        display: inline-block;
        margin-top: 4px;
    }

    .voucher-right {
        display: flex;
        align-items: center;
        padding: 15px;
        border-left: 1px dashed #e5e5e5;
        background: #fafafa;
    }

    .btn-select {
        padding: 8px 16px;
        background: #ee4d2d;
        color: white;
        border: none;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        min-width: 60px;
    }

    .btn-select:hover {
        background: #d73527;
    }

    .voucher-item.selected .btn-select {
        background: #52c41a;
    }

    .voucher-item.selected .btn-select:hover {
        background: #389e0d;
    }

    /* Loading and Error States */
    .loading-state {
        text-align: center;
        padding: 40px 20px;
        color: #666;
    }

    .loading-state i {
        font-size: 24px;
        margin-bottom: 10px;
        color: #ee4d2d;
    }

    .error-state {
        padding: 20px;
    }

    .alert {
        padding: 12px 15px;
        border-radius: 6px;
        margin-bottom: 15px;
    }

    .alert-danger {
        background-color: #fff2f0;
        color: #a8071a;
        border: 1px solid #ffccc7;
    }

    .no-vouchers {
        text-align: center;
        padding: 40px 20px;
        color: #999;
    }



    /* Responsive */
    @media (max-width: 768px) {
        .modal-dialog {
            margin: 10px;
        }
        
        .voucher-item {
            flex-direction: column;
        }
        
        .voucher-left {
            border-bottom: 1px dashed #e5e5e5;
        }
        
        .voucher-right {
            border-left: none;
            border-top: 1px dashed #e5e5e5;
            justify-content: center;
        }
        
        .btn-select {
            width: 100%;
        }
    }
</style>