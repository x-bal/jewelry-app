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
                    <input type="date" name="tanggal" id="tanggal" class="form-control" value="{{ $penarikan->tanggal }}" disabled>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group mb-3">
                    <label for="locator">Locator</label>
                    <input type="text" name="locator" id="locator" class="form-control" value="{{ $penarikan->locator->nama_locator }}" disabled>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group mt-4">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="switch" {{ $penarikan->status == 1 ? 'checked' : '' }}>
                        <label class="form-check-label switch" for="switch">{{ $penarikan->status == 1 ? 'On' : 'Off' }}</label>
                    </div>
                </div>
            </div>
        </form>

        <div class="row mt-3">
            <div class="col-md-12">
                <h3>Data Penarikan</h3>
                <table id="datatable" class="table table-striped table-bordered align-middle">
                    <thead>
                        <tr>
                            <th class="text-nowrap">No</th>
                            <th class="text-nowrap">Tag</th>
                            <th class="text-nowrap">Nama Barang</th>
                            <th class="text-nowrap">Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-detail">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Detail Barang</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
            </div>
            @csrf

            <div class="modal-body row">
                <div class="col-md-12 text-center">
                    <img src="" alt="" class="img-fluid img-barang" width="200">
                </div>
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="rfid">Tag Barang</label>
                        <input type="text" name="rfid" id="rfid" class="form-control" value="" disabled>

                        @error('rfid')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="nama_barang">Nama Barang</label>
                        <input type="text" name="nama_barang" id="nama_barang" class="form-control" value="" disabled>

                        @error('nama_barang')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="tipe">Tipe Barang</label>
                        <input type="text" name="tipe_barang" id="tipe_barang" class="form-control" value="" disabled>

                        @error('tipe')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="kode_barang">Sub Tipe</label>
                        <input type="text" name="kode_barang" id="kode_barang" class="form-control" value="" disabled>

                        @error('kode_barang')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="locator">Locator</label>
                        <input type="text" name="locator" id="nama_locator" class="form-control" value="" disabled>

                        @error('locator')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="berat">Berat</label>
                        <input type="number" step=any name="berat" id="berat" class="form-control" value="" disabled>

                        @error('berat')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="satuan">Satuan</label>
                        <input type="text" step=any name="satuan" id="satuan" class="form-control" value="Gram" disabled>

                        @error('satuan')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="harga">Harga</label>
                        <input type="number" name="harga" id="harga" class="form-control" value="" disabled>

                        @error('harga')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <a href="javascript:;" id="btn-close" class="btn btn-white" data-bs-dismiss="modal">Close</a>
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

    let statusOpname = "{{ $penarikan->status }}";

    if (statusOpname == 1) {
        interv = setInterval(function() {
            $.ajax({
                url: '{{ route("penarikan.view", $penarikan->id) }}',
                method: "GET",
                type: "GET",
                success: function(response) {
                    let brg = response.barang;
                    barangs = JSON.parse(localStorage.getItem("barangs"));

                    for (var i = 0; i < brg.length; i++) {

                        if ($.inArray(brg[i], barangs) > -1) {
                            // console.log(brg[i]);
                        } else {
                            list()
                        }
                    }

                    localStorage.setItem("barangs", JSON.stringify(brg))
                }
            })
        }, 2000)
    } else {
        clearInterval(interv)
    }

    $("#switch").on('click', function() {
        let id = "{{ $penarikan->id }}";
        let status = 0;

        if ($(this).is(":checked")) {
            status = 1;
            interv = setInterval(function() {
                $.ajax({
                    url: '{{ route("penarikan.view", $penarikan->id) }}',
                    method: "GET",
                    type: "GET",
                    success: function(response) {
                        let brg = response.barang;
                        barangs = JSON.parse(localStorage.getItem("barangs"));

                        for (var i = 0; i < brg.length; i++) {

                            if ($.inArray(brg[i], barangs) > -1) {
                                // console.log(brg[i]);
                            } else {
                                list()
                            }
                        }

                        localStorage.setItem("barangs", JSON.stringify(brg))
                    }
                })
            }, 2000)
        } else {
            status = 0;
            clearInterval(interv)
        }

        $.ajax({
            url: '{{ route("penarikan.change") }}',
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
            ajax: "{{ route('penarikan.get', $penarikan->id) }}",
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
                    data: 'nama_barang',
                    name: 'nama_barang'
                },
                {
                    data: 'action',
                    name: 'action'
                },
            ]
        });
    }

    $("#datatable").on('click', '.btn-remove', function(e) {
        e.preventDefault();

        swal({
            title: 'Remove barang dari penarikan?',
            text: 'Remove data barang dari penarikan.',
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
                location.href = $(this).attr("href")
            } else {
                $("#form-delete").attr('action', '')
            }
        });
    })

    $("#datatable").on('click', '.btn-show', function() {
        let route = $(this).attr('data-route')
        let id = $(this).attr('id')

        $.ajax({
            url: "/barang/" + id,
            type: 'GET',
            method: 'GET',
            success: function(response) {
                let barang = response.barang;

                if (barang.rfid == null) {
                    $("#rfid").val(barang.old_rfid)
                } else {
                    $("#rfid").val(barang.rfid)
                }
                let img = "{{ asset('/storage') }}/" + barang.foto;

                $(".img-barang").attr('src', img)
                $("#kode_barang").val(barang.kode_barang)
                $("#tipe_barang").val(response.kodetipe)
                $("#nama_locator").val(response.locator)
                $("#nama_barang").val(barang.nama_barang)
                $("#berat").val(barang.berat)
                $("#satuan").val()
                $("#satuan").val(barang.satuan)
                $("#harga").val(barang.harga)
            }
        })
    })

    list()
</script>
@endpush