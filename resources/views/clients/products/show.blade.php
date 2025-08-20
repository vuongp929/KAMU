@extends('layouts.client')

@section('title', $product->name)

@section('content')
<div class="main product-detail-page">
    <div class="container">
        {{-- SECTION 1: THÔNG TIN CHÍNH CỦA SẢN PHẨM --}}
        <div class="row product-main-info">
            {{-- CỘT TRÁI: THƯ VIỆN ẢNH --}}
            <div class="col-md-5">
                <div class="product-gallery">
                    <div class="main-image-container">
                        {{-- Ảnh chính sẽ được cập nhật bằng JS khi click vào thumbnail --}}
                        <img id="main-product-image" src="{{ $product->thumbnail_url }}" alt="{{ $product->name }}" class="img-responsive" />
                    </div>
                    {{-- Danh sách ảnh thumbnail --}}
                    @if($product->images->count() > 1)
                    <div class="thumbnail-list">
                        @foreach($product->images as $image)
                        <div class="thumbnail-item">
                            <img src="{{ Storage::url($image->image_path) }}" 
                                 data-large-src="{{ Storage::url($image->image_path) }}" 
                                 alt="Thumbnail {{ $loop->iteration }}" class="img-responsive" />
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>

            {{-- CỘT PHẢI: CHI TIẾT VÀ MUA HÀNG --}}
            <div class="col-md-7">
                <div class="product-details">
                    <h1 class="product-title">{{ $product->name }}</h1>
                    
                    {{-- Phần chọn size và giá --}}
                    <div class="variant-selector">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Size</th>
                                    <th class="text-right">Giá bán</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($product->variants as $variant)
                                <tr class="variant-row" data-price="{{ $variant->price }}">
                                    <td>{{ $variant->name }}</td>
                                    <td class="text-right">{{ number_format($variant->price, 0, ',', '.') }}đ</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Các nút bấm mua hàng --}}
                    <form action="{{ route('client.cart.add') }}" method="POST">
                    @csrf

                    {{-- Lựa chọn biến thể (Màu, Size...) --}}
                    <div class="variant-selector-form">
                        <label>Lựa chọn phiên bản:</label>
                        @if($product->variants->isNotEmpty())
                            <select name="variant_id" class="form-control" id="variant-select" required>
                                @foreach($product->variants as $variant)
                                    <option value="{{ $variant->id }}" data-price="{{ $variant->price }}">
                                        {{ $variant->name }}
                                    </option>
                                @endforeach
                            </select>
                        @else
                            <p>Sản phẩm hiện chưa có phiên bản nào.</p>
                        @endif
                    </div>

                    <div class="price-display">
                        <span id="dynamic-price">{{ number_format($product->variants->first()->price ?? 0, 0, ',', '.') }}đ</span>
                    </div>

                    <div class="product-page-cart">
                        <div class="product-quantity">
                            <label for="product-quantity">Số lượng:</label>
                            <input id="product-quantity" type="number" name="quantity" value="1" min="1" class="form-control input-sm">
                        </div>
                        <button class="btn btn-primary" type="submit">
                            <i class="fa fa-shopping-cart"></i> THÊM VÀO GIỎ HÀNG
                        </button>
                    </div>
                </form>

                    {{-- Thông tin thêm và chính sách --}}
                    <div class="extra-info">
                        <ul>
                            <li><i class="fa fa-check-circle"></i> Giao Hàng Nội Thành Siêu Tốc - Giao Đúng Giờ & Tận Tay</li>
                            <li><i class="fa fa-check-circle"></i> Giao Hàng Toàn Quốc 2 - 5 Ngày - Nhận Hàng Mới Phải Trả Tiền</li>
                            <li><i class="fa fa-check-circle"></i> Bảo Hành Đường Chỉ Vĩnh Viễn - Bảo Hành Bông Gấu 1 Năm</li>
                            <li><i class="fa fa-check-circle"></i> Địa Chỉ Shop Dễ Tìm - Có Chỗ Để Xe Ô Tô Miễn Phí</li>
                        </ul>
                    </div>

                    {{-- Thông tin liên hệ --}}
                    <div class="contact-info">
                        <span><i class="fa fa-map-marker"></i> 388 Xã Đàn, Đống Đa, Hà Nội</span>
                        <span><i class="fa fa-phone"></i> 096.5555.346 - 096.2222.346</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- SECTION 2: MÔ TẢ CHI TIẾT VÀ HƯỚNG DẪN --}}
        <div class="row product-description-section">
            <div class="col-md-12">
                <div class="tabs-container">
                    <ul class="nav nav-tabs" role="tablist">
                        <li role="presentation" class="active"><a href="#info" aria-controls="info" role="tab" data-toggle="tab">THÔNG TIN SẢN PHẨM</a></li>
                        <li role="presentation"><a href="#guide" aria-controls="guide" role="tab" data-toggle="tab">HƯỚNG DẪN MUA HÀNG</a></li>
                    </ul>
                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane active" id="info">
                            {!! $product->description !!}
                            <p><strong>Mã sản phẩm:</strong> {{ $product->code }}</p>
                            <p><strong>Kích thước:</strong></p>
                            <ul>
                                @foreach($product->variants as $variant)
                                <li>{{ $variant->name }}: {{ number_format($variant->price, 0, ',', '.') }}đ</li>
                                @endforeach
                            </ul>
                        </div>
                        <div role="tabpanel" class="tab-pane" id="guide">
                            <p>Nội dung hướng dẫn mua hàng của bạn ở đây...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- SECTION 2.5: ĐÁNH GIÁ & BÌNH LUẬN SẢN PHẨM --}}
        <div class="row mt-5 mb-4">
            <div class="col-md-12">
                <div class="review-rating-section p-4 bg-white rounded shadow">
                    <h2 class="h4 font-weight-bold mb-3 text-primary">Đánh giá & Bình luận sản phẩm</h2>
                    @php
                        $reviews = $product->reviews()->where('is_hidden', false)->whereNull('parent_id')->latest()->get();
                        $reviewCount = $reviews->count();
                        $avgStars = $reviews->avg('stars');
                    @endphp
                    <div class="d-flex align-items-center mb-3">
                        <div class="mr-3" style="font-size:2rem;color:#ffc107;">
                            @if($avgStars)
                                @for($i=1;$i<=5;$i++)
                                    @if($i <= round($avgStars))
                                        <i class="fa fa-star"></i>
                                    @else
                                        <i class="fa fa-star-o"></i>
                                    @endif
                                @endfor
                            @else
                                <span class="text-muted">Chưa có đánh giá</span>
                            @endif
                        </div>
                        <div class="ml-2">
                            <span class="h5 mb-0">{{ number_format($avgStars, 2) ?: '0.00' }}/5</span>
                            <span class="text-muted">({{ $reviewCount }} đánh giá)</span>
                        </div>
                    </div>
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    {{-- Danh sách đánh giá --}}
                    <div class="review-list">
                        @forelse($reviews as $review)
                            <div class="review-item">
                                <div class="review-header">
                                    <div class="review-avatar">{{ mb_substr($review->user->name ?? 'Ẩn', 0, 1) }}</div>
                                    <div class="review-meta">
                                        <span class="review-name">{{ $review->user->name ?? 'Ẩn' }}</span>
                                        <span class="review-stars">
                                            @if($review->stars)
                                                @for($i=1;$i<=5;$i++)
                                                    @if($i <= $review->stars)
                                                        <i class="fa fa-star"></i>
                                                    @else
                                                        <i class="fa fa-star-o"></i>
                                                    @endif
                                                @endfor
                                            @endif
                                        </span>
                                    </div>
                                    <span class="review-time">{{ $review->created_at->format('d/m/Y H:i') }}</span>
                                    @auth
                                        @if($review->user_id === auth()->id())
                                            <button class="btn btn-danger btn-sm ml-2 delete-review-btn" data-id="{{ $review->id }}">Xóa</button>
                                        @endif
                                    @endauth
                                </div>
                                <div class="review-content">{{ $review->content }}</div>
                                <div class="review-actions">
                                    @auth
                                    <form action="{{ route('products.reviews.reply', [$product->id, $review->id]) }}" method="POST" style="flex:1;">
                                        @csrf
                                        <textarea name="content" class="form-control mb-1" rows="1" placeholder="Trả lời bình luận này..." required style="border-radius:8px;"></textarea>
                                        <button type="submit" class="btn btn-sm btn-outline-secondary">Trả lời</button>
                                    </form>
                                    @endauth
                                </div>
                                @if($review->replies()->where('is_hidden', false)->count())
                                <div class="reply-list">
                                    @foreach($review->replies()->where('is_hidden', false)->get() as $reply)
                                        <div class="reply-item">
                                            <div class="reply-header">
                                                <div class="reply-avatar">{{ mb_substr($reply->user->name ?? 'Ẩn', 0, 1) }}</div>
                                                <span class="reply-name">{{ $reply->user->name ?? 'Ẩn' }}</span>
                                                <span class="reply-time">{{ $reply->created_at->format('d/m/Y H:i') }}</span>
                                            </div>
                                            <div class="reply-content">{{ $reply->content }}</div>
                                        </div>
                                    @endforeach
                                </div>
                                @endif
                            </div>
                        @empty
                            <div class="text-center text-muted">Chưa có đánh giá nào cho sản phẩm này.</div>
                        @endforelse
                    </div>
                    {{-- Nút mở popup đánh giá --}}
                    @auth
                        @if($canReview)
                            <button type="button" class="btn btn-success mt-4" id="openReviewPopupBtn">
                                <i class="fa fa-pencil"></i> Viết đánh giá
                            </button>
                        @else
                            <div class="mt-4 text-info" style="font-size:1.08rem;">
                                <i class="fa fa-info-circle"></i> Chỉ khách đã mua sản phẩm mới được đánh giá.
                            </div>
                        @endif
                    @endauth
                </div>
            </div>
        </div>

        {{-- SECTION 3: SẢN PHẨM TƯƠNG TỰ --}}
        <div class="row product-section">
            <div class="col-md-12 text-center">
                <div class="section-title">
                    <span>SẢN PHẨM TƯƠNG TỰ</span>
                </div>
            </div>
            @forelse($relatedProducts as $relatedProduct)
                <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
                    @include('clients.product-card', ['product' => $relatedProduct])
                </div>
            @empty
                <p class="text-center col-xs-12">Không có sản phẩm nào tương tự.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
// Popup HTML
const reviewPopupHtml = `
<div id="reviewPopupOverlay" style="position:fixed;z-index:9999;top:0;left:0;width:100vw;height:100vh;background:rgba(30,41,59,0.45);display:flex;align-items:center;justify-content:center;">
  <div id="reviewPopupCard" style="background:#fff;border-radius:20px;box-shadow:0 25px 50px -12px rgba(0,0,0,0.25);max-width:420px;width:95vw;overflow:hidden;animation:popupIn .3s cubic-bezier(.4,2,.6,1);position:relative;">
    <button id="closeReviewPopupBtn" style="position:absolute;top:12px;right:16px;background:none;border:none;font-size:1.5rem;color:#888;cursor:pointer;z-index:2;">&times;</button>
    <div style="padding:32px 24px 24px 24px;">
      <div style="display:flex;align-items:center;gap:16px;margin-bottom:18px;">
        <div style="width:48px;height:48px;border-radius:50%;background:rgba(59,130,246,0.12);display:flex;align-items:center;justify-content:center;color:#2563eb;font-size:2rem;">
          <i class="fa fa-user"></i>
        </div>
        <div>
          <div style="font-weight:700;font-size:18px;">Chia sẻ trải nghiệm của bạn</div>
          <div style="color:#64748b;font-size:13px;">Đánh giá & bình luận giúp cộng đồng</div>
        </div>
      </div>
      <div style="margin-bottom:18px;">
        <label style="font-weight:600;font-size:14px;color:#374151;">Đánh giá của bạn</label>
        <div id="popupStars" style="display:flex;gap:8px;margin:10px 0 0 0;">
          ${[1,2,3,4,5].map(i=>'<span class="popup-star" data-rating="'+i+'" style="font-size:2rem;cursor:pointer;color:#d1d5db;transition:.2s;"><i class="fa fa-star"></i></span>').join('')}
        </div>
        <div id="popupRatingBadge" style="display:none;margin-top:4px;font-size:12px;font-weight:500;color:#92400e;background:#fef3c7;border-radius:12px;padding:2px 10px;border:1px solid #fde68a;display:inline-block;"></div>
      </div>
      <div style="margin-bottom:18px;">
        <label style="font-weight:600;font-size:14px;color:#374151;">Bình luận chi tiết</label>
        <div style="position:relative;">
          <textarea id="popupComment" maxlength="500" style="width:100%;min-height:90px;padding:12px 16px;border:2px solid #e5e7eb;border-radius:12px;font-size:14px;resize:none;outline:none;transition:.2s;"></textarea>
          <div id="popupCharCounter" style="position:absolute;bottom:8px;right:14px;font-size:12px;color:#9ca3af;">0/500</div>
        </div>
      </div>
      <button id="popupSubmitBtn" class="btn btn-primary w-100" style="padding:12px 0;font-weight:500;border-radius:12px;transition:.2s;">Gửi đánh giá</button>
      <div id="popupSuccess" style="display:none;text-align:center;padding:24px 0 0 0;">
        <div style="font-size:2.5rem;color:#10b981;"><i class="fa fa-heart"></i></div>
        <div style="font-size:20px;font-weight:700;margin:8px 0 4px 0;">Cảm ơn bạn!</div>
        <div style="color:#374151;">Đánh giá của bạn đã được gửi thành công</div>
      </div>
    </div>
  </div>
</div>
<style>@keyframes popupIn{0%{transform:scale(.8);opacity:0;}100%{transform:scale(1);opacity:1;}}</style>
`;

function openReviewPopup() {
  if(document.getElementById('reviewPopupOverlay')) return;
  document.body.insertAdjacentHTML('beforeend', reviewPopupHtml);
  let rating = 0;
  const stars = document.querySelectorAll('.popup-star');
  const badge = document.getElementById('popupRatingBadge');
  const comment = document.getElementById('popupComment');
  const charCounter = document.getElementById('popupCharCounter');
  const submitBtn = document.getElementById('popupSubmitBtn');
  const success = document.getElementById('popupSuccess');
  const closeBtn = document.getElementById('closeReviewPopupBtn');
  const badges = {5:'Tuyệt vời!',4:'Rất tốt!',3:'Ổn!',2:'Tạm được',1:'Cần cải thiện'};
  function updateStars(val){
    stars.forEach((s,i)=>{s.style.color=(i<val)?'#fbbf24':'#d1d5db';});
    if(val>0){badge.textContent=badges[val];badge.style.display='inline-block';}else{badge.style.display='none';}
  }
  stars.forEach(s=>{
    s.addEventListener('mouseenter',()=>updateStars(+s.dataset.rating));
    s.addEventListener('mouseleave',()=>updateStars(rating));
    s.addEventListener('click',()=>{rating=+s.dataset.rating;updateStars(rating);});
  });
  comment.addEventListener('input',()=>{charCounter.textContent=comment.value.length+'/500';});
  submitBtn.addEventListener('click',function(){
    if(rating===0||!comment.value.trim()){submitBtn.classList.add('shake');setTimeout(()=>submitBtn.classList.remove('shake'),300);return;}
    submitBtn.disabled=true;submitBtn.textContent='Đang gửi...';
    // AJAX gửi đánh giá
    const productId = {{ $product->id }};
    const url = `/products/${productId}/reviews`;
    const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    $.ajax({
      url: url,
      method: 'POST',
      data: {stars: rating, content: comment.value},
      headers: {'X-CSRF-TOKEN': csrf},
      success: function(res){
        submitBtn.style.display='none';success.style.display='block';
        setTimeout(()=>{closeBtn.click();window.location.reload();}, 1800);
      },
      error: function(xhr){
        submitBtn.disabled=false;submitBtn.textContent='Gửi đánh giá';
        alert('Có lỗi xảy ra khi gửi đánh giá!');
      }
    });
  });
  closeBtn.addEventListener('click',()=>{
    document.getElementById('reviewPopupOverlay').remove();
  });
}
document.addEventListener('DOMContentLoaded',function(){
  const btn = document.getElementById('openReviewPopupBtn');
  if(btn) btn.addEventListener('click',openReviewPopup);
});
$(document).on('click', '.delete-review-btn', function() {
    if (!confirm('Bạn chắc chắn muốn xóa đánh giá này?')) return;
    var reviewId = $(this).data('id');
    var productId = {{ $product->id }};
    $.ajax({
        url: `/products/${productId}/reviews/${reviewId}`,
        type: 'DELETE',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        success: function(res) {
            if (res.success) location.reload();
        },
        error: function() {
            alert('Không thể xóa đánh giá!');
        }
    });
});
</script>
@endpush
@push('styles')
<style>
.review-list {
    display: flex;
    flex-direction: column;
    gap: 24px;
}
.review-item {
    background: #fff;
    border-radius: 18px;
    box-shadow: 0 4px 24px rgba(30,41,59,0.08);
    padding: 20px 24px 16px 24px;
    position: relative;
    transition: box-shadow .2s;
}
.review-item:hover {
    box-shadow: 0 8px 32px rgba(30,41,59,0.14);
}
.review-header {
    display: flex;
    align-items: center;
    gap: 14px;
    margin-bottom: 6px;
}
.review-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg,#e0e7ff 0%,#f0fdfa 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.3rem;
    color: #6366f1;
    font-weight: 700;
    box-shadow: 0 2px 8px rgba(99,102,241,0.08);
}
.review-meta {
    display: flex;
    flex-direction: column;
}
.review-name {
    font-weight: 600;
    color: #1e293b;
    font-size: 1rem;
}
.review-stars {
    color: #fbbf24;
    font-size: 1.1rem;
    letter-spacing: 1px;
}
.review-time {
    color: #94a3b8;
    font-size: 0.92rem;
    margin-left: 2px;
}
.review-content {
    color: #334155;
    font-size: 1.08rem;
    margin: 8px 0 0 0;
    word-break: break-word;
}
.review-actions {
    margin-top: 10px;
    display: flex;
    gap: 10px;
}
.reply-list {
    margin-top: 12px;
    margin-left: 48px;
    display: flex;
    flex-direction: column;
    gap: 10px;
}
.reply-item {
    background: #f8fafc;
    border-radius: 14px;
    box-shadow: 0 2px 8px rgba(30,41,59,0.04);
    padding: 12px 16px 10px 16px;
    position: relative;
}
.reply-header {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 2px;
}
.reply-avatar {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    background: linear-gradient(135deg,#f0fdfa 0%,#e0e7ff 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.95rem;
    color: #0ea5e9;
    font-weight: 700;
}
.reply-name {
    font-weight: 500;
    color: #2563eb;
    font-size: 0.98rem;
}
.reply-time {
    color: #94a3b8;
    font-size: 0.9rem;
}
.reply-content {
    color: #334155;
    font-size: 1rem;
    margin-top: 2px;
}
</style>
@endpush