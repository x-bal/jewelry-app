<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Penjualan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class PenjualanController extends Controller
{
    public function __construct()
    {
        $this->middleware(['can:penjualan-access'])->except('getList');
    }

    public function index()
    {
        $title = 'Data Penjualan';
        $breadcrumbs = ['Penjualan', 'List Penjualan'];

        return view('penjualan.index', compact('title', 'breadcrumbs'));
    }

    public function get(Request $request)
    {
        $role = auth()->user()->roles()->first()->name;
        if ($request->ajax()) {
            if ($role == 'Admin' || $role == 'Owner') {
                $data = Penjualan::get();
            } else {
                $data = Penjualan::where('user_id', auth()->user()->id);
            }

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

    public function destroy(Penjualan $penjualan)
    {
        try {
            DB::beginTransaction();

            foreach ($penjualan->barangs as $barang) {
                if ($barang->rfid != null) {
                    $barang->update([
                        'status' => 'Tersedia',
                        'rfid' => $barang->old_rfid
                    ]);

                    $barang->update([
                        'old_rfid' => null
                    ]);
                }
            }
            $penjualan->barangs()->detach();

            $penjualan->delete();

            DB::commit();

            return back()->with('success', 'Data penjualan berhasil dihapus');
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', $th->getMessage());
        }
    }

    public function getList(Request $request, Penjualan $penjualan)
    {
        if ($request->ajax()) {
            $data = $penjualan->barangs;

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('rfid', function ($row) {
                    return $row->status == 'Tersedia' ?  $row->rfid : $row->old_rfid;
                })
                ->addColumn('kode_barang', function ($row) {
                    return $row->kode_barang;
                })
                ->addColumn('nama_barang', function ($row) {
                    return $row->nama_barang;
                })
                ->addColumn('berat', function ($row) {
                    return $row->berat . ' ' . $row->satuan;
                })
                ->addColumn('harga', function ($row) {
                    return 'Rp. ' . number_format($row->harga, 0, ',', '.');
                })
                ->addColumn('action', function ($row) {
                    $actionBtn = '<button type="button" data-route="' . route('detail-penjualan.destroy', $row->id) . '" class="delete btn btn-danger btn-delete btn-sm">Remove</button>';
                    return $actionBtn;
                })
                ->addColumn('foto', function ($row) {
                    return $row->foto != null ? '<div class="menu-profile-image"><img src="' . asset('/storage/' . $row->foto) . '" alt="" width="35" class="rounded-circle"></div>' : '<div class="menu-profile-image"><img src="' . asset('/img/perhiasan.jpeg') . '" alt="" width="35" class="rounded-circle"></div>';
                })
                ->rawColumns(['rfid', 'kode_barang', 'nama_barang', 'harga', 'berat', 'action', 'foto'])
                ->make(true);
        }
    }

    public function deleteBarang(Barang $barang)
    {
        try {
            DB::beginTransaction();

            $penjualan = Penjualan::find(request('penjualan_id'));
            $penjualan->barangs()->detach([$barang->id]);

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
