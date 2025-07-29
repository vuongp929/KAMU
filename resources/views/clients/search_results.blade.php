@extends('layouts.client')

@section('title', 'Kết Quả Tìm Kiếm')

@section('CSS')

@endsection

@section('content')

@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

<div class="container">
    <h2 class="my-4">Kết Quả Tìm Kiếm: "{{ request()->input('query') }}"</h2>

    @if($products->isEmpty())
        <div class="alert alert-warning">Không tìm thấy sản phẩm nào với từ khóa "{{ request()->input('query') }}"</div>
    @else
        <div class="row">
            @foreach($products as $product)
                <div class="col-md-3" >
                    <a href="{{ route('client.products.show', $product->id) }}">
                    <div class="card">
                        <img src="{{ asset('storage/'.$product->image) }}" class="card-img-top" alt="{{ $product->name }}">
                        <div class="card-body">
                            <h5 class="card-title text-truncate" title="{{ $product->name }}">{{ $product->name }}</h5>

                            <form action="{{ route('client.cart.add') }}" method="POST" onsubmit="return validateSizeSelection({{ $product->id }})">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                <input type="hidden" id="selected-size-{{ $product->id }}" name="size" value="{{ $product->variants->first()->id }}">
                                <input type="hidden" name="redirect_url" value="{{ url()->current() }}">
                                <!-- Nút chọn size -->
                                <div class="size-buttons">
                                    @foreach ($product->variants as $variant)
                                        <button type="button" class="size-button" 
                                            data-size-id="{{ $variant->id }}" 
                                            data-price="{{ $variant->price }}" 
                                            onclick="selectSize({{ $product->id }}, this)">
                                            {{ $variant->size }}
                                        </button>
                                    @endforeach
                                </div>

                                <!-- Giá sản phẩm -->
                                <p class="price mt-2">
                                    Giá: <span id="product-price-{{ $product->id }}">{{ number_format($product->variants->first()->price) }} VND</span>
                                </p>

                                <button type="submit" class="btn btn-primary btn-block mt-3">Thêm vào giỏ</button>
                            </form>                            
                        </div>
                    </div>
                    </a>
                </div>
            @endforeach
        </div>

        {{-- Phân trang --}}
        <div class="d-flex justify-content-center mt-3">
            {{ $products->appends(request()->input())->links() }}
        </div>
    @endif
</div>
@endsection

@section('JS')
<script>
    function selectSize(productId, buttonElement) 
    {
        const sizeButtons = buttonElement.parentElement.querySelectorAll('.size-button');
        sizeButtons.forEach(button => button.classList.remove('selected'));
        buttonElement.classList.add('selected');

        const price = buttonElement.getAttribute('data-price');
        document.getElementById('product-price-' + productId).textContent = new Intl.NumberFormat().format(price) + ' VND';

        const sizeId = buttonElement.getAttribute('data-size-id');
        document.getElementById('selected-size-' + productId).value = sizeId;
    }

    function validateSizeSelection(productId) 
    {
        const selectedSize = document.getElementById('selected-size-' + productId).value;
        if (!selectedSize) {
            alert('Vui lòng chọn size sản phẩm trước khi thêm vào giỏ hàng!');
            return false;
        }
        return true;
    }
</script>
@endsection
