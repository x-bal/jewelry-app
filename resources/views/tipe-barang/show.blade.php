@extends('layouts.master', ['title' => $title, 'breadcrumbs' => $breadcrumbs])

@push('style')
<link href="{{ asset('/') }}plugins/datatables.net-bs4/css/dataTables.bootstrap4.min.css" rel="stylesheet" />
<link href="{{ asset('/') }}plugins/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css" rel="stylesheet" />
@endpush

@section('content')
<div class="panel panel-inverse">
    <div class="panel-heading">
        <h4 class="panel-title">{{ $title }}</h4>
        <div class="panel-heading-btn">
            <a href="javascript:;" class="btn btn-xs btn-icon btn-default" data-toggle="panel-expand"><i class="fa fa-expand"></i></a>
            <a href="javascript:;" class="btn btn-xs btn-icon btn-success" data-toggle="panel-reload"><i class="fa fa-redo"></i></a>
            <a href="javascript:;" class="btn btn-xs btn-icon btn-warning" data-toggle="panel-collapse"><i class="fa fa-minus"></i></a>
            <a href="javascript:;" class="btn btn-xs btn-icon btn-danger" data-toggle="panel-remove"><i class="fa fa-times"></i></a>
        </div>
    </div>

    <div class="panel-body">
        <div class="form-group row mb-3">
            <div class="col-md-3">
                <label for="">Nama Tipe</label>
                <input type="text" name="" id="" class="form-control" value="{{ $tipeBarang->nama_tipe }}" disabled>
            </div>

            <div class="col-md-3">
                <label for="">Kode Tipe</label>
                <input type="text" name="" id="" class="form-control" value="{{ $tipeBarang->kode }}" disabled>
            </div>

            <div class="col-md-3">
                <a href="#modal-dialog" id="btn-add" class="btn btn-primary mt-3" data-route="{{ route('sub-tipe-barang.store') }}" data-bs-toggle="modal"><i class="ion-ios-add"></i> Add Sub Tipe Barang</a>
            </div>
        </div>

        <table id="datatable" class="table table-striped table-bordered align-middle">
            <thead>
                <tr>
                    <th class="text-nowrap">No</th>
                    <th class="text-nowrap">Nama Sub Tipe Barang</th>
                    <th class="text-nowrap">Kode Sub Tipe Barang</th>
                    <th class="text-nowrap">Action</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>

    <div class="modal fade" id="modal-dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Form Tipe Barang</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                </div>
                <form action="" method="post" id="form-tipe">
                    @csrf

                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <input type="hidden" name="tipe_barang" value="{{ $tipeBarang->id }}">

                            <label for="nama_tipe">Nama Sub Tipe</label>
                            <input type="text" name="nama_tipe" id="nama_tipe" class="form-control" value="">

                            @error('nama_tipe')
                            <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="kode">Kode Sub Tipe</label>
                            <input type="text" name="kode" id="kode" class="form-control" value="">

                            @error('kode')
                            <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <div class="modal-footer">
                        <a href="javascript:;" id="btn-close" class="btn btn-white" data-bs-dismiss="modal">Close</a>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <form action="" class="d-none" id="form-delete" method="post">
        @csrf
        @method('DELETE')
    </form>
    @endsection

    @push('script')
    <script src="{{ asset('/') }}plugins/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="{{ asset('/') }}plugins/datatables.net-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="{{ asset('/') }}plugins/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
    <script src="{{ asset('/') }}plugins/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js"></script>
    <script src="{{ asset('/') }}plugins/sweetalert/dist/sweetalert.min.js"></script>

    <script>
        let id = "{{ $tipeBarang->id }}";

        var table = $('#datatable').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: "{{ route('sub-tipe-barang.index') }}?id=" + id,
            deferRender: true,
            pagination: true,
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex'
                },
                {
                    data: 'nama',
                    name: 'nama'
                },
                {
                    data: 'kode',
                    name: 'kode'
                },
                {
                    data: 'action',
                    name: 'action',
                },
            ]
        });

        $("#btn-add").on('click', function() {
            let route = $(this).attr('data-route')
            $("#form-tipe").attr('action', route)
        })

        $("#btn-close").on('click', function() {
            $("#form-tipe").removeAttr('action')
        })

        $("#datatable").on('click', '.btn-edit', function() {
            let route = $(this).attr('data-route')
            let id = $(this).attr('id')

            $("#form-tipe").attr('action', route)
            $("#form-tipe").append(`<input type="hidden" name="_method" value="PUT">`);

            $.ajax({
                url: "/sub-tipe-barang/" + id,
                type: 'GET',
                method: 'GET',
                success: function(response) {
                    let tipe = response.tipeBarang;

                    $("#nama_tipe").val(tipe.nama)
                    $("#kode").val(tipe.kode)
                }
            })
        })

        $("#datatable").on('click', '.btn-delete', function(e) {
            e.preventDefault();
            let route = $(this).attr('data-route')
            $("#form-delete").attr('action', route)

            swal({
                title: 'Hapus sub data tipe?',
                text: 'Menghapus tipe bersifat permanen.',
                icon: 'error',
                buttons: {
                    cancel: {
                        text: 'Cancel',
                        value: null,
                        visible: true,
                        className: 'btn btn-default',
                        closeModal: true,
                    },
                    confirm: {
                        text: 'Yes',
                        value: true,
                        visible: true,
                        className: 'btn btn-danger',
                        closeModal: true
                    }
                }
            }).then((result) => {
                if (result) {
                    $("#form-delete").submit()
                } else {
                    $("#form-delete").attr('action', '')
                }
            });
        })
    </script>
    @endpush