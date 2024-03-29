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
        <a href="#modal-import" class="btn btn-success mb-3" data-bs-toggle="modal"><i class="ion-ios-cloud-upload"></i> Import Barang</a>
        <a href="" class="btn btn-primary mb-3"><i class="ion-ios-cloud-download"></i> Download Template</a>

        <table id="datatable" class="table table-striped table-bordered align-middle">
            <thead>
                <tr>
                    <th class="text-nowrap">No</th>
                    <th class="text-nowrap">Foto</th>
                    <th class="text-nowrap">Tag Barang</th>
                    <th class="text-nowrap">Kode Tipe Barang</th>
                    <th class="text-nowrap">Kode Barang</th>
                    <th class="text-nowrap">Nama Barang</th>
                    <th class="text-nowrap">Locator</th>
                    <th class="text-nowrap">Tipe</th>
                    <th class="text-nowrap">Berat</th>
                    <th class="text-nowrap">Harga</th>
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
                    <h4 class="modal-title">Form Barang</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                </div>
                <form action="" method="post" id="form-barang" enctype="multipart/form-data">
                    @csrf

                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label for="rfid">Tag Barang</label>
                            <input type="text" name="rfid" id="rfid" class="form-control" value="">

                            @error('rfid')
                            <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="kode_barang">Kode Barang</label>
                            <input type="text" name="kode_barang" id="kode_barang" class="form-control" value="">

                            @error('kode_barang')
                            <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="nama_barang">Nama Barang</label>
                            <input type="text" name="nama_barang" id="nama_barang" class="form-control" value="">

                            @error('nama_barang')
                            <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="tipe">Tipe Barang</label>
                            <select name="tipe" id="tipe" class="form-control">
                                <option disabled selected>-- Pilih Tipe --</option>
                                @foreach($tipe as $tip)
                                <option value="{{ $tip->id }}">{{ $tip->nama_tipe }}</option>
                                @endforeach
                            </select>

                            @error('tipe')
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

                        <div class="form-group mb-3">
                            <label for="berat">Berat</label>
                            <input type="number" step=any name="berat" id="berat" class="form-control" value="">

                            @error('berat')
                            <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="satuan">Satuan</label>
                            <input type="text" step=any name="satuan" id="satuan" class="form-control" value="Gram">

                            @error('satuan')
                            <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="harga">Harga</label>
                            <input type="number" name="harga" id="harga" class="form-control" value="">

                            @error('harga')
                            <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="foto">Foto</label>
                            <input type="file" name="foto" id="foto" class="form-control" value="">

                            @error('foto')
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

    <div class="modal fade" id="modal-import">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Form Import Excel</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                </div>
                <form action="{{ route('barang.import') }}" method="post" id="form-import" enctype="multipart/form-data">
                    @csrf

                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label for="file">File</label>
                            <input type="file" name="file" id="file" class="form-control" value="">

                            @error('file')
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
        var table = $('#datatable').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: "{{ route('dummy-barang.list') }}",
            deferRender: true,
            pagination: true,
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
                    data: 'kode_tipe',
                    name: 'kode_tipe'
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
                    data: 'locator',
                    name: 'locator'
                },
                {
                    data: 'tipe',
                    name: 'tipe'
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
                    name: 'action',
                },
            ]
        });

        $("#btn-add").on('click', function() {
            let route = $(this).attr('data-route')
            $("#form-barang").attr('action', route)
        })

        $("#btn-close").on('click', function() {
            $("#form-barang").removeAttr('action')
        })

        $("#datatable").on('click', '.btn-edit', function() {
            let route = $(this).attr('data-route')
            let id = $(this).attr('id')
            // alert(route)

            $("#form-barang").attr('action', route)
            $("#form-barang").append(`<input type="hidden" name="_method" value="PUT">`);

            $.ajax({
                url: "/dummy-barang/" + id,
                type: 'GET',
                method: 'GET',
                success: function(response) {
                    let barang = response.barang;

                    $.each($("#tipe option"), function() {
                        if ($(this).val() == barang.tipe_barang_id) {
                            $(this).attr("selected", "selected");
                        }
                    });

                    $.each($("#locator option"), function() {
                        if ($(this).val() == barang.locator_id) {
                            $(this).attr("selected", "selected");
                        }
                    });

                    $("#rfid").val(barang.rfid)
                    $("#kode_barang").val(barang.kode_barang)
                    $("#nama_barang").val(barang.nama_barang)
                    $("#berat").val(barang.berat)
                    $("#satuan").val('Gram')
                    $("#satuan").val(barang.satuan)
                    $("#harga").val(barang.harga)
                }
            })
        })

        $("#datatable").on('click', '.btn-delete', function(e) {
            e.preventDefault();
            let route = $(this).attr('data-route')
            $("#form-delete").attr('action', route)

            swal({
                title: 'Hapus data barang?',
                text: 'Menghapus barang bersifat permanen.',
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