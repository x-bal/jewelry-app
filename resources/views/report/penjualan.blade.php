@extends('layouts.master', ['title' => $title, 'breadcrumbs' => $breadcrumbs])

@push('style')
<link href="{{ asset('/') }}plugins/datatables.net-bs4/css/dataTables.bootstrap4.min.css" rel="stylesheet" />
<link href="{{ asset('/') }}plugins/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css" rel="stylesheet" />
<link href="{{ asset('/') }}plugins/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css" rel="stylesheet" />
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
        <form action="" class="form-inline row mb-3">
            <div class="form-group col-md-3">
                <label for="from">From</label>
                <input type="date" name="from" id="from" class="form-control" value="{{ request('from') ?? Carbon\Carbon::now()->format('Y-m-d') }}">
            </div>

            <div class="form-group col-md-3">
                <label for="to">To</label>
                <input type="date" name="to" id="to" class="form-control" value="{{ request('to') ?? Carbon\Carbon::now()->format('Y-m-d') }}">
            </div>

            <div class="form-group col-md-6 mt-3">
                <button type="submit" class="btn btn-secondary mt-1"><i class="ion-ios-funnel"></i> Filter</button>
            </div>
        </form>

        <table id="datatable" class="table table-striped table-bordered align-middle">
            <thead>
                <tr>
                    <th class="text-nowrap">No</th>
                    <th class="text-nowrap">Tanggal</th>
                    <th class="text-nowrap">Invoice</th>
                    <th class="text-nowrap">Locator</th>
                    <th class="text-nowrap">Kode Barang</th>
                    <th class="text-nowrap">Nama Barang</th>
                    <th class="text-nowrap">Berat</th>
                    <th class="text-nowrap">Harga</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>
@endsection

@push('script')
<script src="{{ asset('/') }}plugins/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="{{ asset('/') }}plugins/datatables.net-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="{{ asset('/') }}plugins/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
<script src="{{ asset('/') }}plugins/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js"></script>
<script src="{{ asset('/') }}plugins/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
<script src="{{ asset('/') }}plugins/datatables.net-buttons-bs4/js/buttons.bootstrap4.min.js"></script>
<script src="{{ asset('/') }}plugins/datatables.net-buttons/js/buttons.colVis.min.js"></script>
<script src="{{ asset('/') }}plugins/datatables.net-buttons/js/buttons.flash.min.js"></script>
<script src="{{ asset('/') }}plugins/datatables.net-buttons/js/buttons.html5.min.js"></script>
<script src="{{ asset('/') }}plugins/datatables.net-buttons/js/buttons.print.min.js"></script>
<script src="{{ asset('/') }}plugins/pdfmake/build/pdfmake.min.js"></script>
<script src="{{ asset('/') }}plugins/pdfmake/build/vfs_fonts.js"></script>
<script src="{{ asset('/') }}plugins/jszip/dist/jszip.min.js"></script>
<script src="{{ asset('/') }}plugins/sweetalert/dist/sweetalert.min.js"></script>
<script src="{{ asset('/') }}plugins/select2/dist/js/select2.min.js"></script>

<script>
    let from = $("#from").val();
    let to = $("#to").val();

    var table = $('#datatable').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: {
            url: "{{ route('report.penarikan.list') }}",
            type: "GET",
            data: {
                "from": from,
                "to": to,
            }
        },
        deferRender: true,
        pagination: true,
        dom: '<"row"<"col-sm-5"B><"col-sm-7"fr>>t<"row"<"col-sm-5"i><"col-sm-7"p>>',
        buttons: [{
                extend: 'excel',
                className: 'btn-sm btn-success'
            },
            {
                extend: 'pdf',
                className: 'btn-sm btn-danger'
            },
            {
                extend: 'print',
                className: 'btn-sm btn-info'
            }
        ],
        columns: [{
                data: 'DT_RowIndex',
                name: 'DT_RowIndex'
            },
            {
                data: 'tanggal',
                name: 'tanggal'
            },
            {
                data: 'invoice',
                name: 'invoice'
            },
            {
                data: 'locator',
                name: 'locator',
            },
            {
                data: 'kode',
                name: 'kode',
            },
            {
                data: 'nama',
                name: 'nama',
            },
            {
                data: 'berat',
                name: 'berat',
            },
            {
                data: 'harga',
                name: 'harga',
            },
        ]
    });
</script>
@endpush