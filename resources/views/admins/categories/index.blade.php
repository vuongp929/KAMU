@extends('layouts.admin')
@section('title', 'Danh mục sản phẩm')

@section('css')
    <style>
        .modal-backdrop {
            z-index: 1050 !important;
        }

        .modal {
            z-index: 1060 !important;
        }

        .main_content_iner {
            overflow: visible !important;
        }
    </style>
@endsection

@section('content')
    <div class="content">
        <div class="container-xxl">
            <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
                <div class="flex-grow-1">
                    <h4 class="fs-18 fw-semibold m-0">Quản lý danh mục sản phẩm</h4>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <h5 class="card-title mb-0 align-content-center "></h5>
                            <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
                                <i data-feather="plus-square"></i> Thêm danh mục
                            </a>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                @if (session('success'))
                                    <div class="alert alert-success alert-dismissible fade show">
                                        <strong>{{ session('success') }}</strong>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"
                                            aria-label="Close"></button>
                                    </div>
                                @endif

                                <table id="allTable" class="datatable table table-striped mb-0">
                                    <thead>
                                        <tr>
                                            <th scope="col">Hình ảnh</th>
                                            <th scope="col">Tên danh mục</th>
                                            <th scope="col">Trạng thái</th>
                                            <th scope="col">Hành động</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($listCategory as $item)
                                            <tr>
                                                <td>
                                                    <img src="{{ Storage::url($item->image) }}" alt="Hình ảnh sản phẩm"
                                                        width="100px" height="100px" style="object-fit: cover;">
                                                </td>
                                                <td>{{ $item->name }}</td>
                                                <td class="{{ $item->statu ? 'text-success' : 'text-danger' }}">
                                                    {{ $item->statu ? 'Hiển thị' : 'Ẩn' }}
                                                </td>
                                                <td>
                                                <div class="d-flex gap-2">
                                                    {{-- Nút sửa --}}
                                                    <a href="{{ route('admin.categories.edit', $item) }}" 
                                                    class="btn btn-light btn-sm rounded-circle shadow-sm border border-primary text-primary" 
                                                    title="Sửa">
                                                        <i class="bi bi-pencil-fill"></i>
                                                    </a>

                                                    {{-- Nút xóa --}}
                                                    <form method="POST" action="{{ route('admin.categories.destroy', $item) }}" 
                                                        onsubmit="return confirm('Bạn có chắc muốn xoá không vậy ?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" 
                                                                class="btn btn-light btn-sm rounded-circle shadow-sm border border-danger text-danger" 
                                                                title="Xoá">
                                                            <i class="bi bi-trash3-fill"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>


                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- <!-- Modal Xóa -->
            <div class="modal fade" id="removeItemModal" tabindex="-1" aria-labelledby="removeItemModalLabel"
                aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content" style="z-index: 1055;">
                        <div class="modal-header">
                            <h5 class="modal-title" id="removeItemModalLabel">Xác nhận xóa</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            Bạn có chắc chắn muốn xóa danh mục này không? Hành động này không thể hoàn tác.
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                            <form id="deleteForm" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">Xóa</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div> --}}

        </div>
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            $('#allTable').DataTable({
                paging: true,
                searching: true,
                ordering: true,
                info: true,
                pageLength: 8
            });

        //     // Set action form khi mở modal
        //     $('#removeItemModal').on('show.bs.modal', function(event) {
        //         const button = $(event.relatedTarget);
        //         const action = button.data('action');
        //         $('#deleteForm').attr('action', action);
        //     });
        });
    </script>
@endsection
