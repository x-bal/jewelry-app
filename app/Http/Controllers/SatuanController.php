<?php

namespace App\Http\Controllers;

use App\Http\Requests\SatuanRequest;
use App\Models\Satuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class SatuanController extends Controller
{
    public function __construct()
    {
        $this->middleware(['can:master-access']);
    }

    public function index()
    {
        $title = 'Data Satuan';
        $breadcrumbs = ['Master', 'Data Satuan'];

        return view('satuan.index', compact('title', 'breadcrumbs'));
    }

    public function get(Request $request)
    {
        if ($request->ajax()) {
            $data = Satuan::get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $actionBtn = '<a href="#modal-dialog" id="' . $row->id . '" class="btn btn-sm btn-success btn-edit" data-route="' . route('satuan.update', $row->id) . '" data-bs-toggle="modal">Edit</a> <button type="button" data-route="' . route('satuan.destroy', $row->id) . '" class="delete btn btn-danger btn-delete btn-sm">Delete</button>';
                    return $actionBtn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    public function store(SatuanRequest $satuanRequest)
    {
        try {
            DB::beginTransaction();

            $satuan = Satuan::create($satuanRequest->all());

            DB::commit();

            return redirect()->route('satuan.index')->with('success', "{$satuan->nama_satuan} berhasil ditambahkan");
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', $th->getMessage());
        }
    }

    public function show(Satuan $satuan)
    {
        return response()->json([
            'status' => 'success',
            'satuan' => $satuan
        ]);
    }

    public function update(SatuanRequest $satuanRequest, Satuan $satuan)
    {
        try {
            DB::beginTransaction();

            $satuan->update($satuanRequest->all());

            DB::commit();

            return redirect()->route('satuan.index')->with('success', "{$satuan->nama_satuan} berhasil diupdate");
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', $th->getMessage());
        }
    }

    public function destroy(Satuan $satuan)
    {
        try {
            DB::beginTransaction();

            $satuan->delete();

            DB::commit();

            return redirect()->route('satuan.index')->with('success', "{$satuan->nama_satuan} berhasil dihapus");
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', $th->getMessage());
        }
    }
}
