<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Locator;
use App\Models\LostStok;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class LostStokController extends Controller
{
    public function __construct()
    {
        $this->middleware(['can:inventory-access'])->except('lost');
    }

    public function index()
    {
        $title = 'Data Lost Stok';
        $breadcrumbs = ['Lost Stok'];
        $locators = Locator::get();

        return view('lost-stok.index', compact('title', 'breadcrumbs', 'locators'));
    }

    public function get(Request $request)
    {
        if ($request->ajax()) {
            $data = LostStok::whereDate('created_at', $request->tanggal)->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $actionBtn = '<a href="' . route('lost-stok.show', $row->id) . '" id="' . $row->id . '" class="btn btn-sm btn-info">Detail Lost</a> <button type="button" data-route="' . route('lost-stok.destroy', $row->id) . '" class="delete btn btn-danger btn-delete btn-sm">Delete</button>';
                    return $actionBtn;
                })
                ->editColumn('tanggal', function ($row) {
                    return Carbon::parse($row->tanggal)->format('d/m/Y');
                })
                ->addColumn('total', function ($row) {
                    return $row->barangs()->count() . ' Items';
                })
                ->addColumn('locator', function ($row) {
                    return $row->locator->nama_locator;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required',
            'locator' => 'required'
        ]);

        try {
            DB::beginTransaction();

            $lost = LostStok::where([
                'tanggal' => $request->tanggal,
                'locator_id' => $request->locator,
            ])->first();

            if ($lost) {
                $tanggal = Carbon::parse($request->tanggal)->format('d/m/Y');
                return back()->with('error', "Data lost locator tanggal {$tanggal} sudah ada");
            } else {
                LostStok::create([
                    'tanggal' => $request->tanggal,
                    'locator_id' => $request->locator,
                ]);
                $tanggal = Carbon::parse($request->tanggal)->format('d/m/Y');

                DB::commit();

                return back()->with('success', "Data lost locator tanggal {$tanggal} berhasil ditambahkan");
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', $th->getMessage());
        }
    }

    public function show(LostStok $lostStok)
    {
        $title = 'Detail Lost Stok';
        $breadcrumbs = ['Lost Stok', 'Detail'];
        $barangs = Barang::where(['status' => 'Tersedia', 'locator_id' => $lostStok->locator_id])->get();

        return view('lost-stok.show', compact('title', 'breadcrumbs', 'lostStok', 'barangs'));
    }

    public function destroy(LostStok $lostStok)
    {
        try {
            DB::beginTransaction();

            foreach ($lostStok->barangs as $barang) {
                $barang->update([
                    'status' => 'Tersedia',
                    'rfid' => $barang->old_rfid
                ]);

                $barang->update([
                    'old_rfid' => null
                ]);
            }
            $lostStok->barangs()->detach();

            $lostStok->delete();

            DB::commit();

            return back()->with('success', 'Data lost stok berhasil dihapus');
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', $th->getMessage());
        }
    }

    public function lost(LostStok $lostStok)
    {
        if (request()->ajax()) {

            return DataTables::of($lostStok->barangs)
                ->addIndexColumn()
                ->addColumn('nama_barang', function ($row) {
                    return $row->nama_barang;
                })
                ->addColumn('rfid', function ($row) {
                    return $row->old_rfid;
                })
                ->addColumn('kode_barang', function ($row) {
                    return $row->kode_barang;
                })
                ->addColumn('ket', function ($row) {
                    return $row->pivot->ket ?? '-';
                })
                ->addColumn('action', function ($row) {
                    $actionBtn = '<button type="button" data-route="' . route('detail-lost.destroy', $row->id) . '" class="delete btn btn-danger btn-delete btn-sm">Delete</button>';
                    return $actionBtn;
                })
                ->rawColumns(['nama_barang', 'rfid', 'kode_barang', 'action'])
                ->make(true);
        }
    }

    public function addBarang(Request $request)
    {
        $request->validate([
            'barang' => 'required|array',
            'ket' => 'required|string'
        ]);

        try {
            DB::beginTransaction();

            $lost = LostStok::find($request->lost_id);

            $lost->barangs()->attach($request->barang, ['ket' => $request->ket]);

            foreach ($request->barang as $key => $val) {
                $barang = Barang::find($request->barang[$key]);
                $barang->update([
                    'status' => 'Loss',
                    'old_rfid' => $barang->rfid,
                ]);

                $barang->update(['rfid' => null]);
            }

            DB::commit();

            return back()->with('success', "Barang berhasil ditambahkan ke lost stok");
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', $th->getMessage());
        }
    }

    public function deleteBarang(Barang $barang)
    {
        try {
            DB::beginTransaction();

            $lost = LostStok::find(request('lost_id'));
            $lost->barangs()->detach([$barang->id]);
            $barang->update([
                'status' => 'Tersedia',
                'rfid' => $barang->old_rfid,
            ]);
            $barang->update(['old_rfid' => null]);

            DB::commit();

            return back()->with('success', 'Barang berhasil diremove');
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', $th->getMessage());
        }
    }
}
