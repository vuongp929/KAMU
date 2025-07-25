@extends('layouts.admin')
@section('title', 'Quản lý Đánh giá & Bình luận')

@push('styles')
<style>
    .lms_table_active td, .lms_table_active th {
        vertical-align: middle;
        text-align: center;
    }
    .lms_table_active td:nth-child(5) {
        text-align: left;
    }
    .badge_active {
        padding: 5px 12px;
        border-radius: 15px;
        color: white;
        font-size: 11px;
        font-weight: 500;
        text-transform: uppercase;
    }
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="white_box card_height_100 mb_30">
            <div class="box_header">
                <div class="main-title">
                    <h3 class="m-0">Danh sách Đánh giá & Bình luận</h3>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="QA_table">
                <div class="table-responsive">
                    <table class="table lms_table_active table-hover table-bordered">
                        <thead>
                            <tr>
                                <th scope="col">ID</th>
                                <th scope="col">Sản phẩm</th>
                                <th scope="col">Người dùng</th>
                                <th scope="col">Sao</th>
                                <th scope="col" style="text-align: left;">Nội dung</th>
                                <th scope="col">Trạng thái</th>
                                <th scope="col">Ngày tạo</th>
                                <th scope="col">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($reviews as $review)
                                <tr>
                                    <th scope="row">{{ $review->id }}</th>
                                    <td title="{{ $review->product->name ?? '' }}">{{ Str::limit($review->product->name ?? 'N/A', 30) }}</td>
                                    <td>{{ $review->user->name ?? 'Ẩn' }}</td>
                                    <td>
                                        @if($review->stars)
                                            <span style="color: #ffc107;">{{ str_repeat('★', $review->stars) }}</span><span style="color: #e2e8f0;">{{ str_repeat('★', 5 - $review->stars) }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td title="{{ $review->content }}">{{ Str::limit($review->content, 50) }}</td>
                                    <td>
                                        @if($review->is_hidden)
                                            <span class="badge_active" style="background-color: #ff6b6b;">Đã ẩn</span>
                                        @else
                                            <span class="badge_active" style="background-color: #51cda0;">Hiển thị</span>
                                        @endif
                                    </td>
                                    <td>{{ $review->created_at->format('d/m/Y') }}</td>
                                    <td>
                                        <form action="{{ route('admins.reviews.toggleHide', $review->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm {{ $review->is_hidden ? 'btn-success' : 'btn-warning' }}">
                                                {{ $review->is_hidden ? 'Hiện' : 'Ẩn' }}
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center p-4">Chưa có đánh giá nào.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
             <div class="d-flex justify-content-center mt-3">
                {{ $reviews->links() }}
            </div>
        </div>
    </div>
</div>
@endsection 