{{-- Modal mã giảm giá --}}
<div class="modal fade" id="voucherModal" tabindex="-1" aria-labelledby="voucherModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shopee-style">
            <div class="modal-header shopee-header">
                <h5 class="modal-title" id="voucherModalLabel">
                    <i class="fas fa-ticket-alt" style="margin-right: 8px;"></i>Chọn Voucher
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body shopee-body">
                {{-- Applied Discount Display --}}
                <div id="appliedDiscount" class="applied-voucher hidden mb-3">
                    <div class="voucher-applied">
                        <div class="voucher-info">
                            <i class="fas fa-check-circle text-success" style="margin-right: 8px;"></i>
                            <strong>Đã áp dụng: <span class="applied-code"></span></strong>
                            <span class="applied-value text-muted"></span>
                        </div>
                        <button type="button" class="btn btn-sm btn-remove remove-discount-btn">
                            Xóa
                        </button>
                    </div>
                </div>

                {{-- Voucher Input Section --}}
                <div class="voucher-input-section mb-4">
                    <h6 class="section-title mb-3">
                        <i class="fas fa-keyboard" style="margin-right: 8px;"></i>Nhập mã voucher
                    </h6>
                    <div class="input-wrapper">
                        <input type="text" class="voucher-input" id="discountCodeInput" placeholder="Nhập mã giảm giá của bạn" maxlength="20">
                        <button class="btn-apply" type="button" id="applyCodeBtn">
                            Áp dụng
                        </button>
                    </div>
                    <div id="codeResult" class="result-message"></div>
                    
                    {{-- Success message like Shopee --}}
                    <div class="success-message hidden">
                        <i class="fas fa-check-circle"></i>
                        <span>Áp dụng mã giảm giá thành công!</span>
                    </div>
                </div>

                {{-- Voucher Selection Section --}}
                <div class="voucher-selection-section">
                    <h6 class="section-title mb-3">
                        <i class="fas fa-list" style="margin-right: 8px;"></i>Hoặc chọn voucher có sẵn
                    </h6>
                        <div class="voucher-list" id="voucherList">
                            {{-- Voucher Item 1 --}}
                            <div class="voucher-item" data-code="WELCOME10">
                                <div class="voucher-left">
                                    <div class="voucher-icon percent">
                                        <span>10%</span>
                                    </div>
                                    <div class="voucher-details">
                                        <div class="voucher-title">Mã Miễn Phí Vận Chuyển</div>
                                        <div class="voucher-desc">Có thể chọn 1 Voucher</div>
                                        <div class="voucher-condition">Giảm tối đa ₫20k<br>Đơn Tối Thiểu ₫0</div>
                                        <div class="voucher-note">Mã lưu trong hàng trăm ứng dụng Shopee để sử dụng gói đã.</div>
                                    </div>
                                </div>
                                <div class="voucher-right">
                                    <button class="btn-select select-discount-btn" data-code="WELCOME10">
                                        Chọn
                                    </button>
                                </div>
                            </div>

                            {{-- Voucher Item 2 --}}
                            <div class="voucher-item" data-code="FREESHIP">
                                <div class="voucher-left">
                                    <div class="voucher-icon freeship">
                                        <span>FREE<br>SHIP</span>
                                    </div>
                                    <div class="voucher-details">
                                        <div class="voucher-title">Mã Miễn Phí Vận Chuyển</div>
                                        <div class="voucher-desc">Có thể chọn 1 Voucher</div>
                                        <div class="voucher-condition">Giảm tối đa ₫30k<br>Đơn Tối Thiểu ₫45k</div>
                                        <div class="voucher-note">Mã lưu trong hàng trăm ứng dụng Shopee để sử dụng gói đã.</div>
                                    </div>
                                </div>
                                <div class="voucher-right">
                                    <button class="btn-select select-discount-btn" data-code="FREESHIP">
                                        Chọn
                                    </button>
                                </div>
                            </div>

                            {{-- Voucher Item 3 --}}
                            <div class="voucher-item" data-code="SUMMER25">
                                <div class="voucher-left">
                                    <div class="voucher-icon percent">
                                        <span>25%</span>
                                    </div>
                                    <div class="voucher-details">
                                        <div class="voucher-title">Mã Giảm Giá Mùa Hè</div>
                                        <div class="voucher-desc">Có thể chọn 1 Voucher</div>
                                        <div class="voucher-condition">Giảm tối đa ₫100k<br>Đơn Tối Thiểu ₫500k</div>
                                        <div class="voucher-note">Mã lưu trong hàng trăm ứng dụng Shopee để sử dụng gói đã.</div>
                                    </div>
                                </div>
                                <div class="voucher-right">
                                    <button class="btn-select select-discount-btn" data-code="SUMMER25">
                                        Chọn
                                    </button>
                                </div>
                            </div>

                            {{-- Voucher Item 4 --}}
                            <div class="voucher-item" data-code="VIP50K">
                                <div class="voucher-left">
                                    <div class="voucher-icon fixed">
                                        <span>₫50K</span>
                                    </div>
                                    <div class="voucher-details">
                                        <div class="voucher-title">Mã VIP Đặc Biệt</div>
                                        <div class="voucher-desc">Có thể chọn 1 Voucher</div>
                                        <div class="voucher-condition">Giảm ₫50.000<br>Đơn Tối Thiểu ₫800k</div>
                                        <div class="voucher-note">Mã lưu trong hàng trăm ứng dụng Shopee để sử dụng gói đã.</div>
                                    </div>
                                </div>
                                <div class="voucher-right">
                                    <button class="btn-select select-discount-btn" data-code="VIP50K">
                                        Chọn
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div id="selectResult" class="result-message"></div>
                </div>
            </div>
            <div class="modal-footer discount-modal-footer">
                <div class="applied-discount" id="appliedDiscount" style="display: none;">
                    <div class="applied-discount-info">
                        <i class="fas fa-check-circle text-success" style="margin-right: 8px;"></i>
                        <span class="applied-text">Đã áp dụng: </span>
                        <strong class="applied-code"></strong>
                        <span class="applied-value"></span>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-danger remove-discount-btn">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                <button type="button" class="btn btn-primary" id="confirmDiscountBtn" disabled>Xác nhận</button>
            </div>
        </div>
    </div>
</div>

{{-- CSS cho modal --}}
<style>
/* Shopee-style Discount Modal */
.shopee-style {
    border: none;
    border-radius: 8px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
    background: #fff;
    max-width: 600px;
    margin: 0 auto;
}

.shopee-header {
    background: #fff;
    border-bottom: 1px solid #e5e5e5;
    padding: 16px 24px;
    border-radius: 8px 8px 0 0;
}

.shopee-header .modal-title {
    font-size: 18px;
    font-weight: 500;
    color: #333;
    margin: 0;
}

.shopee-header .btn-close {
    background: none;
    border: none;
    font-size: 20px;
    opacity: 0.6;
}

.shopee-body {
    padding: 16px 24px 24px;
    background: #fafafa;
    max-height: 500px;
    overflow-y: auto;
}

/* Applied Voucher */
.applied-voucher {
    background: #fff;
    border: 1px solid #e5e5e5;
    border-radius: 4px;
    padding: 12px;
}

.voucher-applied {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.voucher-info {
    color: #333;
    font-size: 14px;
}

.btn-remove {
    background: #ff5722;
    color: white;
    border: none;
    border-radius: 4px;
    padding: 4px 12px;
    font-size: 12px;
    cursor: pointer;
}

/* Section Titles */
.section-title {
    font-size: 16px;
    font-weight: 600;
    color: #333;
    margin: 0;
    display: flex;
    align-items: center;
}

.section-title i {
    color: #ff5722;
}

/* Voucher Input Section */
.voucher-input-section {
    background: #fff;
    border-radius: 8px;
    padding: 20px;
    border: 1px solid #e5e5e5;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

/* Voucher Selection Section */
.voucher-selection-section {
    background: #fff;
    border-radius: 8px;
    padding: 20px;
    border: 1px solid #e5e5e5;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.input-wrapper {
    display: flex;
    gap: 8px;
    margin-bottom: 12px;
}

.voucher-input {
    flex: 1;
    border: 1px solid #e5e5e5;
    border-radius: 4px;
    padding: 8px 12px;
    font-size: 14px;
    outline: none;
}

.voucher-input:focus {
    border-color: #ff5722;
}

.btn-apply {
    background: #ff5722;
    color: white;
    border: none;
    border-radius: 4px;
    padding: 8px 16px;
    font-size: 14px;
    cursor: pointer;
    white-space: nowrap;
}

.btn-apply:hover {
    background: #e64a19;
}

.success-message {
    background: #e8f5e8;
    color: #2e7d32;
    padding: 8px 12px;
    border-radius: 4px;
    font-size: 14px;
    display: flex;
    align-items: center;
    gap: 8px;
}

/* Voucher List */
.voucher-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.voucher-item {
    background: #fff;
    border: 1px solid #e5e5e5;
    border-radius: 4px;
    padding: 16px;
    display: flex;
    align-items: center;
    transition: all 0.2s;
    cursor: pointer;
}

.voucher-item:hover {
    border-color: #ff5722;
    box-shadow: 0 2px 8px rgba(255, 87, 34, 0.1);
}

.voucher-item.selected {
    border-color: #ff5722;
    background: #fff3f0;
}

.voucher-left {
    display: flex;
    align-items: center;
    flex: 1;
    gap: 12px;
}

.voucher-icon {
    width: 60px;
    height: 60px;
    border-radius: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: bold;
    font-size: 12px;
    text-align: center;
    line-height: 1.2;
    flex-shrink: 0;
}

.voucher-icon.percent {
    background: linear-gradient(135deg, #ff5722, #ff7043);
}

.voucher-icon.freeship {
    background: linear-gradient(135deg, #4caf50, #66bb6a);
    font-size: 10px;
}

.voucher-icon.fixed {
    background: linear-gradient(135deg, #2196f3, #42a5f5);
}

.voucher-details {
    flex: 1;
}

.voucher-title {
    font-size: 14px;
    font-weight: 500;
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
    color: #ff5722;
    margin-bottom: 4px;
    line-height: 1.3;
}

.voucher-note {
    font-size: 11px;
    color: #999;
    line-height: 1.3;
}

.voucher-right {
    margin-left: 12px;
}

.btn-select {
    background: #fff;
    color: #ff5722;
    border: 1px solid #ff5722;
    border-radius: 4px;
    padding: 6px 16px;
    font-size: 12px;
    cursor: pointer;
    transition: all 0.2s;
    min-width: 60px;
}

.btn-select:hover {
    background: #ff5722;
    color: white;
}

.voucher-item.selected .btn-select {
    background: #ff5722;
    color: white;
}

/* Result Messages */
.result-message {
    margin-top: 12px;
    padding: 8px 12px;
    border-radius: 4px;
    font-size: 14px;
    display: none;
}

.result-message.success {
    background: #e8f5e8;
    color: #2e7d32;
    border: 1px solid #c8e6c9;
}

.result-message.error {
    background: #ffebee;
    color: #c62828;
    border: 1px solid #ffcdd2;
}

.discount-modal-footer {
    padding: 20px 30px;
    border-top: 2px solid #f8f9fa;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.applied-discount {
    display: flex;
    align-items: center;
    background: #d4edda;
    padding: 10px 15px;
    border-radius: 10px;
    border: 1px solid #c3e6cb;
}

.applied-discount-info {
    display: flex;
    align-items: center;
    margin-right: 10px;
}

.applied-text {
    color: #155724;
}

.applied-code {
    color: #ea73ac;
    margin-right: 5px;
}

.applied-value {
    color: #155724;
}

.remove-discount-btn {
    border-radius: 50%;
    width: 30px;
    height: 30px;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
}

#confirmDiscountBtn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

/* Responsive */
@media (max-width: 768px) {
    .shopee-style {
        margin: 10px;
        max-width: calc(100% - 20px);
    }
    
    .voucher-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 12px;
    }
    
    .voucher-left {
        width: 100%;
    }
    
    .voucher-right {
        margin-left: 0;
        align-self: flex-end;
    }
}

/* Scrollbar */
.shopee-body::-webkit-scrollbar {
    width: 6px;
}

.shopee-body::-webkit-scrollbar-track {
    background: #f1f1f1;
}

.shopee-body::-webkit-scrollbar-thumb {
    background: #ccc;
    border-radius: 3px;
}

.shopee-body::-webkit-scrollbar-thumb:hover {
    background: #999;
}
</style>