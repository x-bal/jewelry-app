<?php

namespace App\Http\Controllers;

use App\Http\Requests\StokOpnameRequest;
use App\Models\DetailLostStok;
use App\Models\Locator;
use App\Models\LostStok;
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
                ->editColumn('status', function ($row) {
                    if ($row->status == 1) {
                        $checked = 'checked';
                    } else {
                        $checked = '';
                    }
                    return '<div class="form-check form-switch"><input class="form-check-input check-running" type="checkbox" id="switch" data-id="' . $row->id . '" ' . $checked . '></div>';
                })
                ->editColumn('tanggal', function ($row) {
                    return Carbon::parse($row->tanggal)->format('d/m/Y');
                })
                ->addColumn('locator', function ($row) {
                    return $row->locator->nama_locator;
                })
                ->rawColumns(['action', 'status'])
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

    public function find(StokOpname $stokOpname)
    {
        return response()->json([
            'status' => 'success',
            'stok' => $stokOpname
        ], 200);
    }

    public function change(Request $request)
    {
        try {
            DB::beginTransaction();

            $stock = StokOpname::find($request->id);

            $stocks = StokOpname::where('id', '!=', $stock->id)->get();

            foreach ($stocks as $stok) {
                $stok->update(['status' => 0]);
            }

            $stock->update(['status' => $request->status]);

            if ($stock->status == 1) {
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

    public function save(StokOpname $stokOpname)
    {
        try {
            DB::beginTransaction();

            $locator = Locator::find($stokOpname->locator_id);
            $stokBarang = array_column($stokOpname->details()->get()->toArray(), 'barang_id');

            $barangs = $locator->barangs()->whereNotIn('id', $stokBarang)->get();

            $lost = LostStok::create([
                'tanggal' => $stokOpname->tanggal,
                'locator_id' => $stokOpname->locator_id
            ]);

            foreach ($barangs as $barang) {
                $barang->update(['status' => 'Loss']);

                DetailLostStok::updateOrCreate(
                    [
                        'lost_stok_id' => $lost->id
                    ],
                    [
                        'barang_id' => $barang->id
                    ]
                );
            }

            DB::commit();

            return redirect()->route('stok-opname.index')->with('success', "Stok opname tanggal {$stokOpname->tanggal} berhasil disimpan");
        } catch (\Throwable $th) {
            DB::rollBack();
            return $th->getMessage();
            return back()->with('error', $th->getMessage());
        }
    }
}
