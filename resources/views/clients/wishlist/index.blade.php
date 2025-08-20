@extends('layouts.client')

@section('content')
<div class="hero-wrap hero-bread" style="background-image: url({{asset('assets/client/images/bg_2.jpg')}});">
<div class="container">
    <div class="row no-gutters slider-text align-items-center justify-content-center">
      <div class="col-md-9 ftco-animate ">
          <p class="breadcrumbs"><span class="mr-2"><a href="{{ route('home') }}">Trang chủ</a></span> / <span>Yêu thích</span></p>
      </div>
    </div>
  </div>
</div>

<div class="container mt-4">
        <h3 class="mb-0 bread ">Danh Sách Yêu Thích</h3>

    {{-- <h6 class="mb-4 text-center">Danh Sách Yêu Thích</h6> --}}

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($wishlists->isEmpty())
       
        <table class="table">
            <thead class="thead-primary">
                <tr class="text-center">
                    <th>Hình ảnh</th>
                    <th>Tên sản phẩm</th>
                    <th>Giá </th>
                    <th>Thao tác</th>
                </tr>
            </thead>
        </table>
        <p class="text-center">Chưa có sản phẩm nào trong wishlist.</p>
          
    @else
        <div class="row">
            <div class="col-md-12">
                <div class="wishlist-list">
                    <table class="table ">
                        <thead class="thead-primary ">
                            <tr class="">
                                <th>Hình ảnh</th>
                                <th>Tên sản phẩm</th>
                                <th>Giá</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($wishlists as $wishlist)
                            <tr class="">
                                
                                <td class="image-prod">
                                    <div class="img" style="background-image:url('{{ Storage::url($wishlist->product->image) }}'); height: 80px; width: 80px; background-size: cover; background-position: center;"></div>
                                </td>
                                <td class="product-name">
                                    <span>{{ $wishlist->product->name }}</span>
                                </td>
                                <td class="price">
                                    @php
                                        $prices = $wishlist->product->variants->pluck('price')->sort();
                                    @endphp
                                    @if ($prices->count())
                                        <span>
                                            {{ number_format($prices->first()) }} đ - {{ number_format($prices->last()) }} đ
                                        </span>
                                    @else
                                        <span>N/A</span>
                                    @endif
                                </td>


                                <td class="product-remove">
                                    <form action="{{ route('wishlist.remove', $wishlist->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">X</button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
