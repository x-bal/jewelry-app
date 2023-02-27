<?php

namespace App\Http\Controllers;

use App\Http\Requests\StokOpnameRequest;
use App\Models\Locator;
use App\Models\StokOpname;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class StokOpnameController extends Controller
{
    public function index()
    {
        $title = 'Data Stok Opname';
        $breadcrumbs = ['Stok Opname'];
        $locator = Locator::get();

        return view('stok-opname.index', compact('title', 'breadcrumbs', 'locator'));
    }

    public function get(Request $request)
    {
        if ($request->ajax()) {
            $data = StokOpname::get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $actionBtn = '<a href="' . route('stok-opname.show', $row->id) . '" id="' . $row->id . '" class="btn btn-sm btn-info">Input Stok</a> <a href="#modal-dialog" id="' . $row->id . '" class="btn btn-sm btn-success btn-edit" data-route="' . route('stok-opname.update', $row->id) . '" data-bs-toggle="modal">Edit</a> <button type="button" data-route="' . route('stok-opname.destroy', $row->id) . '" class="delete btn btn-danger btn-delete btn-sm">Delete</button>';
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

    public function store(StokOpnameRequest $stokOpnameRequest)
    {
        try {
            DB::beginTransaction();

            $stok = StokOpname::create([
                'tanggal' => $stokOpnameRequest->tanggal,
                'locator_id' => $stokOpnameRequest->locator,
            ]);

            DB::commit();

            return back()->with('success', "Stok tanggal {$stok->tanggal} berhasil ditambahkan");
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }

    public function show(StokOpname $stokOpname)
    {
        $title = 'Input Stok Opname';
        $breadcrumbs = ['Stok Opname', 'Input'];

        return view('stok-opname.show', compact('title', 'breadcrumbs', 'stokOpname'));
    }

    public function change(Request $request)
    {
        try {
            DB::beginTransaction();

            $stok = StokOpname::find($request->id);

            $stok->update(['status' => $request->status]);

            if ($stok->status == 1) {
                $status = 'On';
            } else {
                $status = 'Off';
            }

            DB::commit();

            return response()->json([
                'status' => $status
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function stok(StokOpname $stokOpname)
    {
        if (request()->ajax()) {

            return DataTables::of($stokOpname->details)
                ->addIndexColumn()
                ->addColumn('barang', function ($row) {
                    return $row->barang->nama_barang;
                })
                ->addColumn('rfid', function ($row) {
                    return $row->barang->rfid;
                })
                ->rawColumns(['barang'])
                ->make(true);
        }
    }

    public function unstock(StokOpname $stokOpname)
    {
        if (request()->ajax()) {
            $locator = Locator::find($stokOpname->locator_id);
            $stokBarang = array_column($stokOpname->details()->get()->toArray(), 'barang_id');

            $barang = $locator->barangs()->whereNotIn('id', $stokBarang)->get();

            return DataTables::of($barang)
                ->addIndexColumn()
                ->rawColumns(['barang'])
                ->make(true);
        }
    }
}
