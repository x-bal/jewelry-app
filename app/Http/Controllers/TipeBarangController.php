<?php

namespace App\Http\Controllers;

use App\Http\Requests\TipeBarangRequest;
use App\Models\TipeBarang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class TipeBarangController extends Controller
{
    public function __construct()
    {
        $this->middleware(['can:master-access']);
    }

    public function index()
    {
        $title = 'Data Tipe Barang';
        $breadcrumbs = ['Master', 'Data Tipe Barang'];

        return view('tipe-barang.index', compact('title', 'breadcrumbs'));
    }

    public function get(Request $request)
    {
        if ($request->ajax()) {
            $data = TipeBarang::get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $actionBtn = '<a href="' . route('tipe-barang.detail', $row->id) . '" id="' . $row->id . '" class="btn btn-sm btn-info">Detail</a> <a href="#modal-dialog" id="' . $row->id . '" class="btn btn-sm btn-success btn-edit" data-route="' . route('tipe-barang.update', $row->id) . '" data-bs-toggle="modal">Edit</a> <button type="button" data-route="' . route('tipe-barang.destroy', $row->id) . '" class="delete btn btn-danger btn-delete btn-sm">Delete</button>';
                    return $actionBtn;
                })
                ->editColumn('count', function ($row) {
                    return $row->subs()->count() . ' Sub';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    public function store(TipeBarangRequest $tipeBarangRequest)
    {
        try {
            DB::beginTransaction();

            $tipe_barang = TipeBarang::create($tipeBarangRequest->all());

            DB::commit();

            return redirect()->route('tipe-barang.index')->with('success', "{$tipe_barang->nama_tipe} berhasil ditambahkan");
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', $th->getMessage());
        }
    }

    public function show(TipeBarang $tipeBarang)
    {
        return response()->json([
            'status' => 'success',
            'tipeBarang' => $tipeBarang
        ]);
    }

    public function detail(TipeBarang $tipeBarang)
    {
        $title = 'Detail Tipe Barang';
        $breadcrumbs = ['Master', 'Data Tipe Barang', 'Detail'];

        return view('tipe-barang.show', compact('tipeBarang', 'breadcrumbs', 'title'));
    }

    public function update(TipeBarangRequest $tipeBarangRequest, TipeBarang $tipeBarang)
    {
        try {
            DB::beginTransaction();

            $tipeBarang->update($tipeBarangRequest->all());

            DB::commit();

            return redirect()->route('tipe-barang.index')->with('success', "{$tipeBarang->nama_tipe} berhasil diupdate");
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', $th->getMessage());
        }
    }

    public function destroy(TipeBarang $tipeBarang)
    {
        try {
            DB::beginTransaction();

            $tipeBarang->delete();

            DB::commit();

            return redirect()->route('tipe-barang.index')->with('success', "{$tipeBarang->nama_tipe} berhasil dihapus");
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', $th->getMessage());
        }
    }
}
