<?php

namespace App\Http\Controllers;

use App\Http\Requests\BarangRequest;
use App\Models\Barang;
use App\Models\DummyBarang;
use App\Models\Locator;
use App\Models\TipeBarang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class DummyBarangController extends Controller
{
    public function __construct()
    {
        $this->middleware(['can:master-access']);
    }

    public function index()
    {
        $title = 'Data Dummy Barang';
        $breadcrumbs = ['Master', 'Data Dummy Barang'];
        $tipe = TipeBarang::get();
        $locator = Locator::get();

        return view('dummy-barang.index', compact('title', 'breadcrumbs', 'tipe', 'locator'));
    }

    public function get(Request $request)
    {
        if ($request->ajax()) {
            $data = DummyBarang::get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $actionBtn = '<a href="#modal-dialog" id="' . $row->id . '" class="btn btn-sm btn-success btn-edit" data-route="' . route('dummy-barang.update', $row->id) . '" data-bs-toggle="modal">Edit</a> <button type="button" data-route="' . route('dummy-barang.destroy', $row->id) . '" class="delete btn btn-danger btn-delete btn-sm">Delete</button>';
                    return $actionBtn;
                })
                ->addColumn('tipe', function ($row) {
                    return $row->tipe_barang_id;
                })
                ->addColumn('kode_tipe', function ($row) {
                    return $row->tipe_barang_id;
                })
                ->addColumn('locator', function ($row) {
                    return $row->locator_id;
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

    public function show(DummyBarang $dummyBarang)
    {
        return response()->json([
            'status' => 'success',
            'barang' => $dummyBarang
        ], 200);
    }

    public function update(BarangRequest $barangRequest, DummyBarang $dummyBarang)
    {
        try {
            DB::beginTransaction();
            if ($barangRequest->file('foto')) {
                Storage::delete($dummyBarang->foto);
                $foto = $barangRequest->file('foto');
                $fotoUrl = $foto->storeAs('barang', date('dmy') . '-' . $barangRequest->kode_barang . '.' . $foto->extension());
            } else {
                $fotoUrl = $dummyBarang->foto;
            }

            $barang = Barang::create([
                'rfid' => request('rfid'),
                'kode_barang' => $barangRequest->kode_barang,
                'nama_barang' => $barangRequest->nama_barang,
                'satuan' => $barangRequest->satuan,
                'locator_id' => $barangRequest->locator,
                'tipe_barang_id' => $barangRequest->tipe,
                'harga' => $barangRequest->harga,
                'berat' => $barangRequest->berat,
                'foto' => $fotoUrl
            ]);

            $dummyBarang->delete();

            DB::commit();

            return redirect()->route('dummy-barang.index')->with('success', 'Dummy barang berhasil ditambahkan');
        } catch (\Throwable $th) {
            DB::commit();
            return back()->with('error', $th->getMessage());
        }
    }

    public function destroy(DummyBarang $dummyBarang)
    {
        try {
            DB::beginTransaction();

            $dummyBarang->delete();

            DB::commit();

            return back()->with('success', 'Dummy barang berhasil dihapus');
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }
}
