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
        <form action="" class="form-inline row mb-3">
            <div class="form-group col-md-3">
                <label for="tanggal">Tanggal</label>
                <input type="date" name="tanggal" id="tanggal" class="form-control" value="{{ request('tanggal') ?? Carbon\Carbon::now()->format('Y-m-d') }}">
            </div>

            <div class="form-group col-md-6 mt-3">
                <button type="submit" class="btn btn-secondary mt-1"><i class="ion-ios-funnel"></i> Filter</button>
                <a href="#modal-dialog" id="btn-add" class="btn btn-primary mt-1" data-route="{{ route('stok-opname.store') }}" data-bs-toggle="modal"><i class="ion-ios-add"></i> Add Stok Opname</a>
            </div>
        </form>

        <table id="datatable" class="table table-striped table-bordered align-middle">
            <thead>
                <tr>
                    <th class="text-nowrap">No</th>
                    <th class="text-nowrap">Tanggal</th>
                    <th class="text-nowrap">Locator</th>
                    <th class="text-nowrap">On Running</th>
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
                    <h4 class="modal-title">Form Stok Opname</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                </div>
                <form action="" method="post" id="form-stok-opname">
                    @csrf

                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label for="tanggal">Tanggal</label>
                            <input type="date" name="tanggal" id="tanggal" class="form-control" value="">

                            @error('tanggal')
                            <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="locator">Locator</label>
                            <select name="locator" id="locator" class="form-control">
                                <option disabled selected>-- Pilih Locator --</option>
                                @foreach($locator as $loc)
                                <option value="{{ $loc->id }}">{{ $loc->nama_locator }}</option>
                                @endforeach
                            </select>

                            @error('locator')
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
        let tanggal = $("#tanggal").val();

        var table = $('#datatable').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: {
                url: "{{ route('stok-opname.list') }}",
                type: "GET",
                data: {
                    "tanggal": tanggal
                }
            },
            deferRender: true,
            pagination: true,
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex'
                },
                {
                    data: 'tanggal',
                    name: 'tanggal'
                },
                {
                    data: 'locator',
                    name: 'locator'
                },
                {
                    data: 'status',
                    name: 'status'
                },
                {
                    data: 'action',
                    name: 'action',
                },
            ]
        });

        $("#btn-add").on('click', function() {
            let route = $(this).attr('data-route')
            $("#form-stok-opname").attr('action', route)
        })

        $("#btn-close").on('click', function() {
            $("#form-stok-opname").removeAttr('action')
        })

        $("#datatable").on('click', '.btn-edit', function() {
            let route = $(this).attr('data-route')
            let id = $(this).attr('id')

            $("#form-stok-opname").attr('action', route)
            $("#form-stok-opname").append(`<input type="hidden" name="_method" value="PUT">`);

            $.ajax({
                url: "/stok-opname/" + id + '/find',
                type: 'GET',
                method: 'GET',
                success: function(response) {
                    let stok = response.stok;

                    $("#tanggal").val(stok.tanggal)
                    $.each($("#locator option"), function() {
                        if ($(this).val() == stok.locator_id) {
                            $(this).attr("selected", "selected");
                        }
                    });
                }
            })
        })

        $("#datatable").on('click', '.check-running', function() {
            let id = $(this).attr('data-id');
            let status = 0;

            if ($(this).is(":checked")) {
                status = 1;
            } else {
                status = 0;
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
                    // $(".switch").empty().append(response.status)
                }

            })
        })

        $("#datatable").on('click', '.btn-delete', function(e) {
            e.preventDefault();
            let route = $(this).attr('data-route')
            $("#form-delete").attr('action', route)

            swal({
                title: 'Hapus data stok opname?',
                text: 'Menghapus stok opname bersifat permanen.',
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