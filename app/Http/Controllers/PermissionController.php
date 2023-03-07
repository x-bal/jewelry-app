<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Yajra\DataTables\DataTables;

class PermissionController extends Controller
{
    public function index()
    {
        $title = 'Data Permission';
        $breadcrumbs = ['Access', 'Data Permission'];

        return view('permission.index', compact('title', 'breadcrumbs'));
    }

    public function get(Request $request)
    {
        if ($request->ajax()) {
            $data = Permission::get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $actionBtn = '<a href="#modal-dialog" id="' . $row->id . '" class="btn btn-sm btn-success btn-edit" data-route="' . route('locators.update', $row->id) . '" data-bs-toggle="modal">Edit</a> <button type="button" data-route="' . route('locators.destroy', $row->id) . '" class="delete btn btn-danger btn-delete btn-sm">Delete</button>';
                    return $actionBtn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $permission = Permission::create([
                'name' => $request->name,
                'guard' => 'web'
            ]);

            DB::commit();

            return redirect()->route('permissions.index')->with('success', "{$permission->name} berhasil ditambahkan");
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', $th->getMessage());
        }
    }

    public function show(Permission $permission)
    {
        return response()->json([
            'status' => 'success',
            'permission' => $permission
        ]);
    }

    public function update(Request $request, Permission $permission)
    {
        try {
            DB::beginTransaction();

            $permission->update($request->all());

            DB::commit();

            return redirect()->route('permissions.index')->with('success', "{$permission->name} berhasil diupdate");
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', $th->getMessage());
        }
    }

    public function destroy(Permission $permission)
    {
        try {
            DB::beginTransaction();

            $permission->delete();

            DB::commit();

            return redirect()->route('permissions.index')->with('success', "{$permission->name} berhasil dihapus");
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', $th->getMessage());
        }
    }
}
