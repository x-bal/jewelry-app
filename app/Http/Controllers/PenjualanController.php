<?php

namespace App\Http\Controllers;

use App\Models\Penjualan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class PenjualanController extends Controller
{
    public function index()
    {
        $title = 'Data Penjualan';
        $breadcrumbs = ['Penjualan', 'List Penjualan'];

        return view('penjualan.index', compact('title', 'breadcrumbs'));
    }

    public function get(Request $request)
    {
        if ($request->ajax()) {
            $data = Penjualan::get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('tanggal', function ($row) {
                    return Carbon::parse($row->tanggal)->format('d/m/Y');
                })
                ->addColumn('kasir', function ($row) {
                    return $row->user->name;
                })
                ->addColumn('action', function ($row) {
                    $actionBtn = '<a href="' . route('penjualan.edit', $row->id) . '" id="' . $row->id . '" class="btn btn-sm btn-success ">Edit</a> <button type="button" data-route="' . route('penjualan.destroy', $row->id) . '" class="delete btn btn-danger btn-delete btn-sm">Delete</button>';
                    return $actionBtn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    public function create()
    {
        $title = 'Add Penjualan';
        $breadcrumbs = ['Penjualan', 'Add Penjualan'];
        $penjualan = null;
        $type = 'Add';

        $last = Penjualan::where(['user_id' => auth()->user()->id, 'status' => 'Input'])->latest()->first();

        if ($last) {
            $penjualan = $last;
        } else {
            $penjualan = Penjualan::create([
                'user_id' => auth()->user()->id,
                'tanggal' => date('Y-m-d'),
                'invoice' => 'INV/' . date('dmy/') . rand(1000, 9999)
            ]);
        }

        return view('penjualan.form', compact('title', 'breadcrumbs', 'penjualan', 'type'));
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $penjualan = Penjualan::find($request->id);

            $penjualan->update(['status' => 'Selesai']);

            DB::commit();

            return redirect()->route('penjualan.index')->with('success', 'Penjualan berhasil diinput');
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', $th->getMessage());
        }
    }

    public function show(Penjualan $penjualan)
    {
        //
    }

    public function edit(Penjualan $penjualan)
    {
        $title = 'Edit Penjualan';
        $breadcrumbs = ['Penjualan', 'Edit Penjualan'];
        $type = 'Edit';

        return view('penjualan.form', compact('title', 'breadcrumbs', 'penjualan', 'type'));
    }

    public function update(Request $request, Penjualan $penjualan)
    {
        //
    }

    public function destroy(Penjualan $penjualan)
    {
        //
    }

    public function getList(Request $request, Penjualan $penjualan)
    {
        if ($request->ajax()) {
            $data = $penjualan->details;

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('rfid', function ($row) {
                    return $row->barang->rfid;
                })
                ->addColumn('kode_barang', function ($row) {
                    return $row->barang->kode_barang;
                })
                ->addColumn('nama_barang', function ($row) {
                    return $row->barang->nama_barang;
                })
                ->addColumn('berat', function ($row) {
                    return $row->barang->berat . ' ' . $row->barang->satuan->nama_satuan;
                })
                ->addColumn('harga', function ($row) {
                    return 'Rp. ' . number_format($row->barang->harga, 0, ',', '.');
                })
                ->rawColumns(['rfid', 'kode_barang', 'nama_barang', 'harga', 'berat'])
                ->make(true);
        }
    }
}
