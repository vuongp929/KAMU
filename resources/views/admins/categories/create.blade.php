@extends('layouts.admin')
@section('title', 'Thêm danh mục sản phẩm')
@section('css')

@endsection

@section('content')
<div class="content">

    <!-- Start Content-->
    <div class="container-xxl">

        <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
            <div class="flex-grow-1">
                <h4 class="fs-18 fw-semibold m-0">Quản lý danh mục sản phẩm</h4>
            </div>
        </div>
        <div class="row">
        <!-- Striped Rows -->
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                        <form action="{{ route('admin.categories.store') }}" method="post" enctype="multipart/form-data">
                            @csrf
                        <div class="row">

                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Tên danh mục</label>
                                    <input type="text" id="name" name="name" class="form-control 
                                    @error('name') is-invalid @enderror" value="{{ old('name') }}"
                                    placeholder="Tên danh mục">

                                    @error('name')
                                        <p class="text-danger">{{ $message }}</p>
                                    @enderror
                                </div>
                                {{-- <div class="mb-3">
                                    <label for="slug" class="form-label">Tên danh mục</label>
                                    <input type="hidden" id="slug" name="slug" class="form-control 
                                    @error('name') is-invalid @enderror" value="{{ old('name') }}"
                                    placeholder="Tên danh mục">

                                    @error('name')
                                        <p class="text-danger">{{ $message }}</p>
                                    @enderror
                                </div> --}}
                                <div>

                                    <label for="statu" class="form-label">Trạng thái</label>
                                    <div class="mb-3">
                                        <label for="parent_id" class="form-label">Danh mục cha (Tùy chọn)</label>
                                        <select name="parent_id" id="parent_id" class="form-select">
                                            <option value="">-- Không có --</option>
                                            @foreach($listCategory as $cate)
                                                <option value="{{ $cate->id }}">{{ $cate->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-sm-10 mb-3 d-flex gap-2 ">
                                        
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="statu" id="gridRadios1" value="1" checked>
                                            <label class="form-check-label text-success" for="gridRadios1">
                                                Hiển thị
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="statu" id="gridRadios2" value="0">
                                            <label class="form-check-label text-danger" for="gridRadios2">
                                                Ẩn
                                            </label>
                                        </div>
                                       
                                  </div>
                                </div>
                        </div>

                        <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="image-select" class="form-label">Hình ảnh </label>
                                    <input type="file" id="image" name="image" class="form-control" onchange="showImage(event)">
                                    <img class="mt-2" src="" id="img_danh_muc" alt="Hình ảnh sản phẩm" style="width: 150px; display:none ;">
                                </div>
                        </div>
                        <div class="d-flex justify-content-center">
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                        </form>
                    </div>
                </div><!-- end card header -->

            </div>
        </div>
        </div>
                           
     

    </div> <!-- container-fluid -->
</div> <!-- content -->
@endsection

@section('js')
    <script>
        function showImage(event){
            const img_danh_muc = document.getElementById('img_danh_muc');


            const file = event.target.files[0];

            const reader = new FileReader();


            reader.onload = function () {
                img_danh_muc.src = reader.result ;
                img_danh_muc.style.display = "block" ;
            }

            if (file) {
               reader.readAsDataURL(file) ; 
            }
        }
    </script>
@endsection
