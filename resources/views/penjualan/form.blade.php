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
        <form action="{{ route('penjualan.store') }}" method="post" class="row">
            @csrf
            <input type="hidden" name="id" value="{{ $penjualan->id }}">
            <div class="col-md-3">
                <div class="form-group mb-3">
                    <label for="tanggal">Tanggal</label>
                    <input type="date" name="tanggal" id="tanggal" class="form-control" value="{{ $penjualan->tanggal }}" readonly>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group mb-3">
                    <label for="invoice">Invoice</label>
                    <input type="text" name="invoice" id="invoice" class="form-control" value="{{ $penjualan->invoice  }}" readonly>
                </div>
            </div>
            @if($type == 'Add')
            <div class="col-md-6">
                <div class="form-group mt-3">
                    <button type="submit" class="btn mt-1 btn-sm btn-primary">Submit</button>
                </div>
            </div>
            @endif
        </form>

        <div class="row mt-3">
            <div class="col-md-12">
                <h3>Data Barang</h3>
                <table id="datatable" class="table table-striped table-bordered align-middle">
                    <thead>
                        <tr>
                            <th class="text-nowrap">No</th>
                            <th class="text-nowrap">Tag</th>
                            <th class="text-nowrap">Kode Barang</th>
                            <th class="text-nowrap">Nama Barang</th>
                            <th class="text-nowrap">Berat</th>
                            <th class="text-nowrap">Harga</th>
                            <th class="text-nowrap">Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<form action="" class="d-none" id="form-delete" method="post">
    <input type="hidden" name="penjualan_id" value="{{ $penjualan->id }}">
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
    let route = "{{ route('penjualan.get', $penjualan->id) }}";

    function list(route) {
        $('#datatable').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: route,
            deferRender: true,
            pagination: true,
            bDestroy: true,
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex'
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
                    data: 'berat',
                    name: 'berat'
                },
                {
                    data: 'harga',
                    name: 'harga'
                },
                {
                    data: 'action',
                    name: 'action'
                },
            ]
        });
    }

    function listEdit(route) {
        $('#datatable').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: route,
            deferRender: true,
            pagination: true,
            bDestroy: true,
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex'
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
                    data: 'berat',
                    name: 'berat'
                },
                {
                    data: 'harga',
                    name: 'harga'
                },
                {
                    data: 'action',
                    name: 'action'
                },
            ]
        });
    }

    if ("{{ $type }}" == 'Add') {
        setInterval(function() {
            list(route)
        }, 3000)
    } else {
        listEdit(route)
    }

    $("#btn-add").on('click', function() {
        let route = $(this).attr('data-route')
        $("#form-penjualan").attr('action', route)
    })

    $("#btn-close").on('click', function() {
        $("#form-penjualan").removeAttr('action')
    })

    $("#datatable").on('click', '.btn-delete', function(e) {
        e.preventDefault();
        let route = $(this).attr('data-route')
        $("#form-delete").attr('action', route)

        swal({
            title: 'Remove data barang?',
            text: 'Remove barang dari detail penjualan.',
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