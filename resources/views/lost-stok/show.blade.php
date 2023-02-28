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
        <form action="" method="post" class="row">
            <div class="col-md-3">
                <div class="form-group mb-3">
                    <label for="tanggal">Tanggal</label>
                    <input type="date" name="tanggal" id="tanggal" class="form-control" value="{{ $lostStok->tanggal }}" disabled>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group mb-3">
                    <label for="locator">Locator</label>
                    <input type="text" name="locator" id="locator" class="form-control" value="{{ $lostStok->locator->nama_locator }}" disabled>
                </div>
            </div>
        </form>

        <div class="row mt-3">
            <div class="col-md-12">
                <h3>Data Lost Stok</h3>
                <table id="datatable" class="table table-striped table-bordered align-middle">
                    <thead>
                        <tr>
                            <th class="text-nowrap">No</th>
                            <th class="text-nowrap">Tag</th>
                            <th class="text-nowrap">Kode Barang</th>
                            <th class="text-nowrap">Nama Barang</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script src="{{ asset('/') }}plugins/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="{{ asset('/') }}plugins/datatables.net-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="{{ asset('/') }}plugins/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
<script src="{{ asset('/') }}plugins/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js"></script>
<script src="{{ asset('/') }}plugins/sweetalert/dist/sweetalert.min.js"></script>

<script>
    var interv = null;

    $("#switch").on('click', function() {
        let id = "{{ $lostStok->id }}";
        let status = 0;

        if ($(this).is(":checked")) {
            status = 1;
            interv = setInterval(function() {
                list()
                listNo()
            }, 2000)
        } else {
            status = 0;
            clearInterval(interv)
        }

        $.ajax({
            url: '{{ route("stok-opname.change") }}',
            type: 'GET',
            method: "GET",
            data: {
                id: id,
                status: status
            },
            success: function(response) {
                $(".switch").empty().append(response.status)
            }

        })
    });

    function list() {
        $('#datatable').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: "{{ route('lost-stok.lost', $lostStok->id) }}",
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
            ]
        });
    }

    list()
</script>
@endpush