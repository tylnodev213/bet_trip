@extends('layouts.admin')
@section('admin')
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-5 align-self-center">
                <h4 class="page-title">Place</h4>
                <div class="d-flex align-items-center">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('tours.index') }}">Tour</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('itineraries.index', $itinerary->tour_id) }}">Hành trình</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Danh sách điểm đến</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <a class="btn btn-info mb-3"
                       href="{{ route('places.create',[$itinerary->tour_id, $itinerary->id]) }}" class="text-white">
                        Thêm mới
                    </a>
                    <table class="table table-striped table-bordered m-t-10" id="placeTable">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Tên</th>
                            <th>Mô tả</th>
                            <th>Thao tác</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script>
        $(document).ready(function () {
            let datatable = $('#placeTable').DataTable({
                processing: true,
                responsive: true,
                serverSide: true,
                searching: false,
                stateSave: true,
                ordering: false,
                ajax: {
                    url: "{!! route('places.data',[$itinerary->tour_id, $itinerary->id]) !!}",
                },

                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                    {data: 'name', name: 'name'},
                    {data: 'description', name: 'description', width: '65%'},
                    {data: 'action', name: 'action', className: 'align-middle text-center', width: 65},
                ],
                columnDefs: [
                    {className: 'align-middle', targets: '_all'},
                ],
            });

            $('#placeTable thead th').removeClass('align-middle text-center');

            // Evenet Delete Itinerary
            $(document).on('click', '.delete', function (e) {
                e.preventDefault();
                let link = $(this).attr("href");
                const swalWithBootstrapButtons = Swal.mixin({
                    customClass: {
                        confirmButton: 'btn btn-success m-2',
                        cancelButton: 'btn btn-danger m-2'
                    },
                    buttonsStyling: false
                })
                swalWithBootstrapButtons.fire({
                    title: 'Bạn có chắc?',
                    text: "Bạn không thể hoàn tác lại!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ok, tôi muốn xóa!',
                    cancelButtonText: 'Không!',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax(
                            {
                                url: link,
                                type: 'delete',
                                success: function (response) {
                                    toastr.success('Xóa thành công !');
                                    datatable.ajax.reload(null, false);
                                },
                                error: function (response) {
                                    toastr.error('Xóa thất bại !')
                                }
                            });
                    } else if (
                        result.dismiss === Swal.DismissReason.cancel
                    ) {
                        swalWithBootstrapButtons.fire(
                            'Cancelled',
                            '',
                            'error'
                        )
                    }
                })
            })
        });
    </script>
@endsection
