<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

@extends('layouts.client')

@section('title', 'Chi Tiết Sản Phẩm')

@section('content')
    <div class="container">
        <div id="content" class="site-content">
        <ul id="breadcrumb" class="breadcrumb">
            <li class="breadcrumb-item home"><a href="/" title="Trang chủ">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="https://teddy.vn/blindbox-corner" title="Blindbox Corner">Blindbox Corner</a></li>
            <li class="breadcrumb-item"><a href="https://teddy.vn/blindbox-corner/baby-three" title="Baby Three">Baby Three</a></li>
            <li class="breadcrumb-item active">Blindbox Baby Three 1000%</li>
        </ul>
        <section class="module module-home-services mb-3">
    <div class="flex justify-between">
        <div class="w-1/4 p-2">
            <a class="home-service flex flex-col items-center" href="#" rel="nofollow">
                <img class="w-24 h-24" src="https://teddy.vn/wp-content/uploads/2017/07/Artboard-16-1-e1661254198715.png" alt="Giao Hàng Tận Nhà" />
                <strong class="title mt-2 text-center">Giao Hàng Tận Nhà</strong>
            </a>
        </div>
        <div class="w-1/4 p-2">
            <a class="home-service flex flex-col items-center" href="#" rel="nofollow">
                <img class="w-24 h-24" src="https://teddy.vn/wp-content/uploads/2017/07/Artboard-16-copy-1.png" alt="Gói Quà Siêu Đẹp" />
                <strong class="title mt-2 text-center">Gói Quà Siêu Đẹp</strong>
            </a>
        </div>
        <div class="w-1/4 p-2">
            <a class="home-service flex flex-col items-center" href="#" rel="nofollow">
                <img class="w-24 h-24" src="https://teddy.vn/wp-content/uploads/2017/07/Artboard-16-copy-2-1.png" alt="Cách Giặt Gấu Bông" />
                <strong class="title mt-2 text-center">Cách Giặt Gấu Bông</strong>
            </a>
        </div>
        <div class="w-1/4 p-2">
            <a class="home-service flex flex-col items-center" href="#" rel="nofollow">
                <img class="w-24 h-24" src="https://teddy.vn/wp-content/uploads/2018/04/Artboard-16-copy-3-1.png" alt="Bảo Hành Gấu Bông" />
                <strong class="title mt-2 text-center">Bảo Hành Gấu Bông</strong>
            </a>
        </div>
    </div>
</section>
    <!-- </div>
        <h1>{{ $product->name }}</h1>
        <img src="{{ asset('storage/'.$product->image) }}" alt="{{ $product->name }}">
        <p>Giá: {{ number_format($product->price) }} VND</p>
        <p>{{ $product->description }}</p>
    </div> -->
    <div class="flex flex-wrap">
    <!-- Product Image Column -->
    <div class="w-full md:w-1/2 p-4">
        <img src="{{ asset('storage/'.$product->image) }}" alt="Product Image" class="w-full h-auto rounded-lg shadow-lg" />
    </div>

    <!-- Product Details Column -->
    <div class="w-full md:w-1/2 p-4">
        <h1 class="text-3xl font-bold text-red-500 mb-2">{{ $product->name }}</h1>

        <form action="{{ route('cart.add') }}" method="POST" onsubmit="return validateSizeSelection({{ $product->id }})">
            @csrf
            <input type="hidden" name="product_id" value="{{ $product->id }}">
            <input type="hidden" id="selected-size" name="size" value="{{ optional($product->variants->first())->id }}">
            <input type="hidden" name="redirect_url" value="{{ url()->current() }}">

            <div class="grid grid-cols-2 gap-4 mb-4">
                @foreach($product->variants as $variant)
                    <div class="flex items-center mb-2">
                        <input type="radio" id="variant-{{ $variant->id }}" name="variant" class="hidden peer"
                               data-size-id="{{ $variant->id }}"
                               data-price="{{ $variant->price }}"
                               data-remaining-stock="{{ $variant->stock }}"
                               onclick="selectSize({{ $product->id }}, this)">
                        <label for="variant-{{ $variant->id }}" class="cursor-pointer bg-gray-200 text-gray-700 rounded-lg py-3 px-5 text-lg transition duration-200 ease-in-out hover:bg-gray-300 peer-checked:bg-blue-500 peer-checked:text-white">
                            {{ $variant->size }}
                        </label>
                        <span class="ml-4 text-lg font-semibold">{{ number_format($variant->price) }} VND</span>
                        <span class="ml-2 text-gray-500">Còn lại: {{ $variant->stock }} cái</span>
                    </div>
                @endforeach
            </div>

            <p class="mt-4 text-2xl font-bold">Giá: <span id="product-price" class="text-red-600 text-3xl">{{ number_format(optional($product->variants->first())->price ?? 0) }} VND</span></p>

            <!-- Quantity Input -->
            <div class="mt-4">
                <label for="quantity" class="block text-gray-700">Số lượng:</label>
                <input type="number" id="quantity" name="quantity" min="1" value="1" class="border rounded-lg p-2 w-full" required>
            </div>

            <button type="submit" class="btn btn-add btn-block mt-3 bg-blue-500 text-white rounded-lg py-3 text-lg hover:bg-blue-600 transition duration-200 ease-in-out">Đặt hàng</button>
        </form>

        <p class="mt-4 text-gray-600">{{ $product->description }}</p>
    </div>
</div>

{{-- Đánh giá & Bình luận sản phẩm --}}
<div class="mt-10">
    <h2 class="text-2xl font-bold mb-4 text-blue-700">Đánh giá & Bình luận</h2>
    @if(session('success'))
        <div class="alert alert-success mb-4">{{ session('success') }}</div>
    @endif
    {{-- Form gửi đánh giá --}}
    @auth
    <form action="{{ route('products.reviews.store', $product->id) }}" method="POST" class="mb-6 bg-gray-50 p-4 rounded-lg shadow">
        @csrf
        <div class="flex items-center mb-2">
            <label class="mr-2 font-semibold">Số sao:</label>
            <select name="stars" class="border rounded p-1">
                <option value="">-- Chọn --</option>
                @for($i=1;$i<=5;$i++)
                    <option value="{{ $i }}">{{ $i }} ★</option>
                @endfor
            </select>
        </div>
        <textarea name="content" class="form-control w-full border rounded p-2 mb-2" rows="2" placeholder="Nhập bình luận..." required></textarea>
        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Gửi đánh giá</button>
    </form>
    @else
    <div class="mb-4">
        <a href="{{ route('login') }}" class="text-blue-600 underline">Đăng nhập để đánh giá</a>
    </div>
    @endauth

    {{-- Danh sách đánh giá --}}
    <div class="space-y-4">
        @foreach($product->reviews()->where('is_hidden', false)->whereNull('parent_id')->latest()->get() as $review)
            <div class="bg-white p-4 rounded shadow">
                <div class="flex items-center mb-1">
                    <span class="font-semibold text-gray-800">{{ $review->user->name ?? 'Ẩn' }}</span>
                    @if($review->stars)
                        <span class="ml-2 text-yellow-500">{{ str_repeat('★', $review->stars) }}</span>
                    @endif
                    <span class="ml-2 text-gray-400 text-xs">{{ $review->created_at->format('d/m/Y H:i') }}</span>
                </div>
                <div class="mb-2">{{ $review->content }}</div>
                {{-- Trả lời bình luận --}}
                @auth
                <form action="{{ route('products.reviews.reply', [$product->id, $review->id]) }}" method="POST" class="mb-2">
                    @csrf
                    <textarea name="content" class="form-control w-full border rounded p-1 mb-1" rows="1" placeholder="Trả lời bình luận này..." required></textarea>
                    <button type="submit" class="text-xs bg-gray-200 px-2 py-1 rounded hover:bg-gray-300">Trả lời</button>
                </form>
                @endauth
                {{-- Hiển thị các trả lời --}}
                @foreach($review->replies()->where('is_hidden', false)->get() as $reply)
                    <div class="ml-6 mt-2 p-2 bg-gray-50 border-l-4 border-blue-200 rounded">
                        <span class="font-semibold text-blue-700">{{ $reply->user->name ?? 'Ẩn' }}</span>
                        <span class="ml-2 text-gray-400 text-xs">{{ $reply->created_at->format('d/m/Y H:i') }}</span>
                        <div>{{ $reply->content }}</div>
                    </div>
                @endforeach
            </div>
        @endforeach
    </div>
</div>
@endsection
@section('JS')
<script>
    // Hàm chọn size và cập nhật giao diện
    function selectSize(productId, element) {
    const selectedSizeId = element.getAttribute('data-size-id');
    const selectedPrice = element.getAttribute('data-price');

    // Update the hidden input for the selected size
    document.getElementById('selected-size').value = selectedSizeId;

    // Update the displayed price
    document.getElementById('product-price').innerText = new Intl.NumberFormat().format(selectedPrice) + ' VND';
}
    function validateSizeSelection(productId) {
        const selectedSizeId = document.querySelector('input[name="variant"]:checked').getAttribute('data-size-id');
        const quantity = document.getElementById('quantity').value;
        const remainingStock = parseInt(document.querySelector(`input[data-size-id="${selectedSizeId}"]`).getAttribute('data-remaining-stock'));

        if (quantity > remainingStock) {
            alert(`Số lượng bạn chọn vượt quá số lượng còn lại (${remainingStock}). Vui lòng chọn lại.`);
            return false; // Prevent form submission
        }
        return true; // Allow form submission
    }
</script>
@endsection
