<?php

namespace App\Http\Controllers;

use App\Http\Requests\TipeBarangRequest;
use App\Models\SubTipeBarang;
use App\Models\TipeBarang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class SubTipeBarangController extends Controller
{

    public function index(Request $request)
    {
        $tipeBarang = TipeBarang::find($request->id);

        if ($request->ajax()) {
            $data = $tipeBarang->subs;

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $actionBtn = '<a href="#modal-dialog" id="' . $row->id . '" class="btn btn-sm btn-success btn-edit" data-route="' . route('sub-tipe-barang.update', $row->id) . '" data-bs-toggle="modal">Edit</a> <button type="button" data-route="' . route('sub-tipe-barang.destroy', $row->id) . '" class="delete btn btn-danger btn-delete btn-sm">Delete</button>';
                    return $actionBtn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TipeBarangRequest $tipeBarangRequest)
    {
        try {
            DB::beginTransaction();

            $tipe_barang = SubTipeBarang::create([
                'tipe_barang_id' => request('tipe_barang'),
                'kode' => $tipeBarangRequest->kode,
                'nama' => $tipeBarangRequest->nama_tipe,
            ]);

            DB::commit();

            return back()->with('success', "{$tipe_barang->nama} berhasil ditambahkan");
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', $th->getMessage());
        }
    }

    public function show(SubTipeBarang $subTipeBarang)
    {
        return response()->json([
            'status' => 'success',
            'tipeBarang' => $subTipeBarang
        ]);
    }

    public function edit(SubTipeBarang $subTipeBarang)
    {
        //
    }

    public function update(Request $request, SubTipeBarang $subTipeBarang)
    {
        try {
            DB::beginTransaction();

            $subTipeBarang->update([
                'tipe_barang_id' => request('tipe_barang'),
                'kode' => $request->kode,
                'nama' => $request->nama_tipe,
            ]);

            DB::commit();

            return back()->with('success', "{$subTipeBarang->nama} berhasil diupdate");
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', $th->getMessage());
        }
    }

    public function destroy(SubTipeBarang $subTipeBarang)
    {
        //
    }
}
