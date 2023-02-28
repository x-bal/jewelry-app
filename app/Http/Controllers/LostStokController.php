<?php

namespace App\Http\Controllers;

use App\Models\LostStok;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class LostStokController extends Controller
{
    public function index()
    {
        $title = 'Data Lost Stok';
        $breadcrumbs = ['Lost Stok'];

        return view('lost-stok.index', compact('title', 'breadcrumbs'));
    }

    public function get(Request $request)
    {
        if ($request->ajax()) {
            $data = LostStok::get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $actionBtn = '<a href="' . route('lost-stok.show', $row->id) . '" id="' . $row->id . '" class="btn btn-sm btn-info">Detail Lost</a>';
                    return $actionBtn;
                })
                ->editColumn('tanggal', function ($row) {
                    return Carbon::parse($row->tanggal)->format('d/m/Y');
                })
                ->addColumn('locator', function ($row) {
                    return $row->locator->nama_locator;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    public function show(LostStok $lostStok)
    {
        $title = 'Detail Lost Stok';
        $breadcrumbs = ['Lost Stok', 'Detail'];

        return view('lost-stok.show', compact('title', 'breadcrumbs', 'lostStok'));
    }

    public function lost(LostStok $lostStok)
    {
        if (request()->ajax()) {

            return DataTables::of($lostStok->details)
                ->addIndexColumn()
                ->addColumn('nama_barang', function ($row) {
                    return $row->barang->nama_barang;
                })
                ->addColumn('rfid', function ($row) {
                    return $row->barang->rfid;
                })
                ->addColumn('kode_barang', function ($row) {
                    return $row->barang->kode_barang;
                })
                ->rawColumns(['nama_barang', 'rfid', 'kode_barang'])
                ->make(true);
        }
    }
}
