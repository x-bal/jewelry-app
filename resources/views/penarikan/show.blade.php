@extends('layouts.master', ['title' => $title, 'breadcrumbs' => $breadcrumbs])

@push('style')
<link href="{{ asset('/') }}plugins/datatables.net-bs4/css/dataTables.bootstrap4.min.css" rel="stylesheet" />
<link href="{{ asset('/') }}plugins/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css" rel="stylesheet" />
<link href="{{ asset('/') }}plugins/select2/dist/css/select2.min.css" rel="stylesheet" />
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
        <form action="" method="post" class="row">
            <div class="col-md-3">
                <div class="form-group mb-3">
                    <label for="tanggal">Tanggal</label>
                    <input type="date" name="tanggal" id="tanggal" class="form-control" value="{{ $penarikan->tanggal }}" disabled>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group mb-3">
                    <label for="locator">Locator</label>
                    <input type="text" name="locator" id="locator" class="form-control" value="{{ $penarikan->locator->nama_locator }}" disabled>
                </div>
            </div>
        </form>

        <div class="row mt-3">
            <div class="col-md-12">
                <a href="#modal-dialog" id="btn-add" class="btn btn-primary mb-3" data-route="{{ route('detail-penarikan.add') }}" data-bs-toggle="modal"><i class="ion-ios-add"></i> Add Barang</a>

                <h3>Data Penarikan Barang</h3>
                <table id="datatable" class="table table-striped table-bordered align-middle">
                    <thead>
                        <tr>
                            <th class="text-nowrap">No</th>
                            <th class="text-nowrap">Foto</th>
                            <th class="text-nowrap">Tag</th>
                            <th class="text-nowrap">Kode Barang</th>
                            <th class="text-nowrap">Nama Barang</th>
                            <th class="text-nowrap">Keterangan</th>
                            <th class="text-nowrap">Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Form Penarikan Barang</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
            </div>
            <form action="" method="post" id="form-penarikan">
                @csrf

                <div class="modal-body">
                    <input type="hidden" name="penarikan_id" value="{{ $penarikan->id }}">

                    <div class="form-group mb-3">
                        <label for="barang">Barang</label>
                        <select name="barang[]" id="barang" class="form-control multiple-select2" multiple>
                            @foreach($barangs as $barang)
                            <option value="{{ $barang->id }}">{{ $barang->kode_barang }} - {{ $barang->nama_barang }}</option>
                            @endforeach
                        </select>

                        @error('barang')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="ket">Keterangan</label>
                        <input type="text" name="ket" id="ket" class="form-control">

                        @error('ket')
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
    <input type="hidden" name="penarikan_id" value="{{ $penarikan->id }}">
</form>
@endsection

@push('script')
<script src="{{ asset('/') }}plugins/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="{{ asset('/') }}plugins/datatables.net-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="{{ asset('/') }}plugins/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
<script src="{{ asset('/') }}plugins/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js"></script>
<script src="{{ asset('/') }}plugins/sweetalert/dist/sweetalert.min.js"></script>
<script src="{{ asset('/') }}plugins/select2/dist/js/select2.min.js"></script>

<script>
    $(".multiple-select2").select2({
        dropdownParent: $('#modal-dialog'),
        placeholder: "Pilih Barang",
        allowClear: true
    })

    $("#btn-add").on('click', function() {
        let route = $(this).attr('data-route')
        $("#form-penarikan").attr('action', route)
    })

    $("#datatable").on('click', '.btn-delete', function(e) {
        e.preventDefault();
        let route = $(this).attr('data-route')
        $("#form-delete").attr('action', route)

        swal({
            title: 'Remove data barang?',
            text: 'Remove data barang dari penarikan?',
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

    function list() {
        $('#datatable').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: "{{ route('penarikan.get', $penarikan->id) }}",
            deferRender: true,
            pagination: true,
            bDestroy: true,
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex'
                },
                {
                    data: 'foto',
                    name: 'foto'
                },
                {
                    data: 'rfid',
                    name: 'rfid'
                },
                {
                    data: 'kode_barang',
                    name: 'kode_barang'
                },
                {
                    data: 'nama_barang',
                    name: 'nama_barang'
                },
                {
                    data: 'ket',
                    name: 'ket'
                },
                {
                    data: 'action',
                    name: 'action'
                },
            ]
        });
    }

    list()
</script>
@endpush