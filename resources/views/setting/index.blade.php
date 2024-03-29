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

    <form action="{{ route('setting.update') }}" method="post" enctype="multipart/form-data">
        <div class="panel-body row">
            @csrf
            <div class="col-md-6">
                <div class="form-group mb-3">
                    <label for="title">Title</label>
                    <input type="text" name="title" id="title" class="form-control" value="{{ $setting->val }}">
                </div>

                <div class="form-group mb-3">
                    <label for="tagline">Tagline</label>
                    <input type="text" name="tagline" id="tagline" class="form-control" value="{{ $tagline->val }}">
                </div>

                <div class="form-group mb-3">
                    <label for="alert">Sound Alert</label>
                    <input type="file" name="alert" id="alert" class="form-control" value="">
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group mb-3">
                    <label for="url">Url Server</label>
                    <input type="text" name="url" id="url" class="form-control" value="{{ $url->val }}">
                </div>

                <div class="form-group mb-3">
                    <label for="foto">Foto Login</label>
                    <input type="file" name="foto" id="foto" class="form-control" value="">
                </div>
            </div>

            <div class="col-md-12">
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Update</button>
                    <a href="{{ route('syncdb') }}" class="btn btn-success"><i class="ion-ios-upload"></i> Sync to Server</a>
                </div>
            </div>
        </div>
    </form>
</div>
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
        ajax: "{{ route('permissions.list') }}",
        deferRender: true,
        pagination: true,
        columns: [{
                data: 'DT_RowIndex',
                name: 'DT_RowIndex'
            },
            {
                data: 'name',
                name: 'name'
            },
        ]
    });
</script>
@endpush