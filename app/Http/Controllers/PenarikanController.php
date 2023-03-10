<?php

namespace App\Http\Controllers;

use App\Http\Requests\PenarikanRequest;
use App\Models\Barang;
use App\Models\Locator;
use App\Models\Penarikan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class PenarikanController extends Controller
{
    public function __construct()
    {
        $this->middleware(['can:inventory-access']);
    }

    public function index()
    {
        $title = 'Data Penarikan';
        $breadcrumbs = ['Penarikan', 'List Penarikan'];
        $locators = Locator::get();

        return view('penarikan.index', compact('title', 'breadcrumbs', 'locators'));
    }

    public function get(Request $request)
    {
        if ($request->ajax()) {
            $data = Penarikan::whereDate('created_at', $request->tanggal)->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('tanggal', function ($row) {
                    return Carbon::parse($row->tanggal)->format('d/m/Y');
                })
                ->addColumn('locator', function ($row) {
                    return $row->locator->nama_locator;
                })
                ->addColumn('total', function ($row) {
                    return $row->barangs()->count() . ' Items';
                })
                ->addColumn('action', function ($row) {
                    $actionBtn = '<a href="' . route('penarikan.show', $row->id) . '" id="' . $row->id . '" class="btn btn-sm btn-info ">Detail</a> <button type="button" data-route="' . route('penarikan.destroy', $row->id) . '" class="delete btn btn-danger btn-delete btn-sm">Delete</button>';
                    return $actionBtn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    public function store(PenarikanRequest $penarikanRequest)
    {
        try {
            DB::beginTransaction();

            $penarikan = Penarikan::where([
                'tanggal' => $penarikanRequest->tanggal,
                'locator_id' => $penarikanRequest->locator,
            ])->first();

            if ($penarikan) {
                $tanggal = Carbon::parse($penarikanRequest->tanggal)->format('d/m/Y');
                return back()->with('error', "Data penarikan tanggal {$tanggal} sudah ada");
            } else {
                Penarikan::create([
                    'tanggal' => $penarikanRequest->tanggal,
                    'locator_id' => $penarikanRequest->locator,
                ]);

                $tanggal = Carbon::parse($penarikanRequest->tanggal)->format('d/m/Y');

                DB::commit();

                return back()->with('success', "Data Penarikan tanggal {$tanggal} berhasil ditambahkan");
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', $th->getMessage());
        }
    }

    public function show(Penarikan $penarikan)
    {
        $title = 'Detail Penarikan';
        $breadcrumbs = ['Penarikan', 'Detail'];
        $barangs = Barang::where(['status' => 'Tersedia', 'locator_id' => $penarikan->locator_id])->get();

        return view('penarikan.show', compact('title', 'breadcrumbs', 'penarikan', 'barangs'));
    }

    public function getBarang(Penarikan $penarikan)
    {
        if (request()->ajax()) {

            return DataTables::of($penarikan->barangs)
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
                    $actionBtn = '<button type="button" data-route="' . route('detail-penarikan.destroy', $row->id) . '" class="delete btn btn-danger btn-delete btn-sm">Delete</button>';
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

            $penarikan = Penarikan::find($request->penarikan_id);

            $penarikan->barangs()->attach($request->barang, ['ket' => $request->ket]);

            foreach ($request->barang as $key => $val) {
                $barang = Barang::find($request->barang[$key]);
                $barang->update([
                    'status' => 'Ditarik',
                    'old_rfid' => $barang->rfid,
                ]);

                $barang->update(['rfid' => null]);
            }

            DB::commit();

            return back()->with('success', "Barang berhasil ditambahkan ke penarikan");
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', $th->getMessage());
        }
    }

    public function destroy(Penarikan $penarikan)
    {
        try {
            DB::beginTransaction();

            foreach ($penarikan->barangs as $barang) {
                $barang->update([
                    'status' => 'Tersedia',
                    'rfid' => $barang->old_rfid
                ]);

                $barang->update([
                    'old_rfid' => null
                ]);
            }
            $penarikan->barangs()->detach();

            $penarikan->delete();

            DB::commit();

            return back()->with('success', 'Data penarikan berhasil dihapus');
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', $th->getMessage());
        }
    }

    public function deleteBarang(Barang $barang)
    {
        try {
            DB::beginTransaction();

            $penarikan = Penarikan::find(request('penarikan_id'));
            $penarikan->barangs()->detach([$barang->id]);

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
