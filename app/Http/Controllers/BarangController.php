<?php

namespace App\Http\Controllers;

use App\Http\Requests\BarangRequest;
use App\Models\Barang;
use App\Models\Locator;
use App\Models\Satuan;
use App\Models\TipeBarang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class BarangController extends Controller
{
    public function index()
    {
        $title = 'Data Barang';
        $breadcrumbs = ['Master', 'Data Barang'];
        $satuan = Satuan::get();
        $tipe = TipeBarang::get();
        $locator = Locator::get();

        return view('barang.index', compact('title', 'breadcrumbs', 'satuan', 'tipe', 'locator'));
    }

    public function get(Request $request)
    {
        if ($request->ajax()) {
            $data = Barang::get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $actionBtn = '<a href="#modal-dialog" id="' . $row->id . '" class="btn btn-sm btn-success btn-edit" data-route="' . route('barang.update', $row->id) . '" data-bs-toggle="modal">Edit</a> <button type="button" data-route="' . route('barang.destroy', $row->id) . '" class="delete btn btn-danger btn-delete btn-sm">Delete</button>';
                    return $actionBtn;
                })
                ->addColumn('tipe', function ($row) {
                    return $row->tipeBarang->nama_tipe;
                })
                ->addColumn('locator', function ($row) {
                    return $row->locator->nama_locator;
                })
                ->editColumn('berat', function ($row) {
                    return $row->berat . ' ' . $row->satuan->nama_satuan;
                })
                ->editColumn('harga', function ($row) {
                    return 'Rp. ' . number_format($row->harga, 0, ',', '.');
                })
                ->rawColumns(['action', 'satuan', 'tipe', 'locator'])
                ->make(true);
        }
    }

    public function show(Barang $barang)
    {
        return response()->json([
            'status' => 'success',
            'barang' => $barang
        ], 200);
    }

    public function update(BarangRequest $barangRequest, Barang $barang)
    {
        try {
            DB::beginTransaction();

            $barang->update([
                'nama_barang' => $barangRequest->nama_barang,
                'satuan_id' => $barangRequest->satuan,
                'locator_id' => $barangRequest->locator,
                'tipe_barang_id' => $barangRequest->tipe,
                'harga' => $barangRequest->harga,
                'berat' => $barangRequest->berat,
            ]);

            DB::commit();

            return redirect()->route('barang.index')->with('success', 'Barang berhasil diupdate');
        } catch (\Throwable $th) {
            DB::commit();
            return back()->with('error', $th->getMessage());
        }
    }
}