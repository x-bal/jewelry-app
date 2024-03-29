<?php

namespace App\Http\Controllers;

use App\Http\Requests\BarangRequest;
use App\Imports\BarangImport;
use App\Models\Barang;
use App\Models\Locator;
use App\Models\SubTipeBarang;
use App\Models\TipeBarang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;;

class BarangController extends Controller
{
    public function __construct()
    {
        $this->middleware(['can:master-access']);
    }

    public function index()
    {
        $title = 'Data Barang';
        $breadcrumbs = ['Master', 'Data Barang'];
        $tipe = TipeBarang::get();
        $subtipe = SubTipeBarang::get();
        $locator = Locator::get();

        return view('barang.index', compact('title', 'breadcrumbs', 'tipe', 'subtipe', 'locator'));
    }

    public function get(Request $request)
    {
        if ($request->ajax()) {
            $data = Barang::where('status', 'Tersedia');

            return DataTables::eloquent($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $actionBtn = '<a href="#modal-dialog" id="' . $row->id . '" class="btn btn-sm btn-success btn-edit" data-route="' . route('barang.update', $row->id) . '" data-bs-toggle="modal">Edit</a> <button type="button" data-route="' . route('barang.destroy', $row->id) . '" class="delete btn btn-danger btn-delete btn-sm">Delete</button>';
                    return $actionBtn;
                })
                ->addColumn('tipe', function ($row) {
                    return $row->tipeBarang->nama;
                })
                ->addColumn('kode_tipe', function ($row) {
                    return $row->tipeBarang->tipe->kode;
                })
                ->addColumn('locator', function ($row) {
                    return $row->locator->nama_locator;
                })
                ->editColumn('berat', function ($row) {
                    return $row->berat . ' ' . $row->satuan;
                })
                ->editColumn('harga', function ($row) {
                    return 'Rp. ' . number_format($row->harga, 0, ',', '.');
                })
                ->addColumn('foto', function ($row) {
                    return $row->foto != null ? '<div class="menu-profile-image"><img src="' . asset('/storage/' . $row->foto) . '" alt="" width="35" class="rounded-circle"></div>' : '<div class="menu-profile-image"><img src="' . asset('/img/perhiasan.jpeg') . '" alt="" width="35" class="rounded-circle"></div>';
                })
                ->rawColumns(['action', 'satuan', 'tipe', 'locator', 'foto'])
                ->make(true);
        }
    }

    public function show(Barang $barang)
    {
        return response()->json([
            'status' => 'success',
            'barang' => $barang,
            'locator' => $barang->locator->nama_locator,
            'tipe' => $barang->tipeBarang->tipe->id,
            'kodetipe' => $barang->tipeBarang->tipe->kode,
            'subtipe' => $barang->tipeBarang->kode,
        ], 200);
    }

    public function update(BarangRequest $barangRequest, Barang $barang)
    {
        try {
            DB::beginTransaction();
            if ($barangRequest->file('foto')) {
                Storage::delete($barang->foto);
                $foto = $barangRequest->file('foto');
                $fotoUrl = $foto->storeAs('barang', date('dmy') . '-' . $barangRequest->kode_barang . '.' . $foto->extension());
            } else {
                $fotoUrl = $barang->foto;
            }

            $sub = SubTipeBarang::find($barangRequest->tipe);

            $barang->update([
                'kode_barang' => $sub->kode,
                'nama_barang' => $barangRequest->nama_barang,
                'satuan' => $barangRequest->satuan,
                'locator_id' => $barangRequest->locator,
                'sub_tipe_barang_id' => $barangRequest->subtipe,
                'harga' => $barangRequest->harga,
                'berat' => $barangRequest->berat,
                'foto' => $fotoUrl
            ]);

            DB::commit();

            return redirect()->route('barang.index')->with('success', 'Barang berhasil diupdate');
        } catch (\Throwable $th) {
            DB::commit();
            return back()->with('error', $th->getMessage());
        }
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xls,xlsx'
        ]);

        try {
            DB::beginTransaction();

            Excel::import(new BarangImport, $request->file('file'));

            DB::commit();

            return back()->with('success', "Data barang berhasil diimport");
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', $th->getMessage());
        }
    }

    public function download()
    {
        try {
            $file = public_path('/example-import.xlsx');

            return Response::download($file);
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }
}
