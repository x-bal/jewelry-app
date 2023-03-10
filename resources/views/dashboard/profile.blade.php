@extends('layouts.master')

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
        <form action="{{ route('profile.update', $user->id) }}" method="post">
            @method('PUT')
            @csrf

            <div class="form-group mb-3">
                <label for="username">Username</label>
                <input type="text" name="username" id="username" class="form-control" value="{{ $user->username }}">

                @error('username')
                <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group mb-3">
                <label for="name">Name</label>
                <input type="text" name="name" id="name" class="form-control" value="{{ $user->name }}">

                @error('name')
                <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group mb-3">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" class="form-control" value="">

                @error('password')
                <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group mb-3">
                <label for="role">Role</label>
                <input type="role" name="role" id="role" class="form-control" value="{{ $user->roles()->first()->name ?? '-' }}" readonly>

                @error('role')
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

            <div class="form-group">
                <button type="submit" class="btn btn-primary"><i class="ion-ios-save"></i> Save</button>
            </div>
        </form>
    </div>


</div>
@endsection