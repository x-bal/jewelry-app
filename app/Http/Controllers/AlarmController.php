<?php

namespace App\Http\Controllers;

use App\Models\Alarm;
use App\Models\Barang;
use App\Models\Locator;
use App\Models\LostStok;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class AlarmController extends Controller
{
    public function __construct()
    {
        $this->middleware(['can:inventory-access'])->except('getBarang');
    }

    public function index()
    {
        $title = 'Data Barang Hilang';
        $breadcrumbs = ['Barang Hilang', 'List Barang Hilang'];

        return view('loss.index', compact('title', 'breadcrumbs',));
    }

    public function get(Request $request)
    {
        if ($request->ajax()) {
            $data = Alarm::all();

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('tanggal', function ($row) {
                    return Carbon::parse($row->created_at)->format('d/m/Y');
                })
                ->editColumn('nama_barang', function ($row) {
                    return $row->barang->nama_barang;
                })
                ->editColumn('rfid', function ($row) {
                    return $row->barang->rfid != null ? $row->barang->rfid : $row->barang->old_rfid;
                })
                ->addColumn('action', function ($row) {
                    $actionBtn = '<a href="#modal-detail" id="' . $row->barang->id . '" class="btn btn-sm btn-info btn-show" data-bs-toggle="modal"><i class="ion-ios-eye"></i></a> <button type="button" id="' . $row->id . '" data-route="' . route('loss.store') . '" class="btn btn-primary btn-add btn-sm"><i class="ion-ios-add"></i></a></button> <button type="button" data-route="' . route('loss.destroy', $row->id) . '" class="btn btn-danger btn-delete btn-sm"><i class="ion-ios-close"></i></a></button>';
                    return $actionBtn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    public function destroy(Alarm $loss)
    {
        try {
            DB::beginTransaction();

            $loss->barang->update([
                'status' => 'Tersedia',
                'rfid' => $loss->barang->old_rfid,
            ]);

            $loss->barang->update([
                'old_rfid' => null
            ]);

            $loss->delete();

            DB::commit();

            return back()->with('success', "Barang berhasil diremove");
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', $th->getMessage());
        }
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $loss = Alarm::find($request->alarm_id);
            $barang = Barang::find($loss->barang_id);
            $locator = Locator::find($barang->locator_id);
            $date = Carbon::parse($loss->created_at)->format('Y-m-d');
            $lossStok = LostStok::whereDate('tanggal', $date)->where('locator_id', $locator->id)->first();

            if ($lossStok) {
                DB::table('barang_lost_stok')->insert([
                    'lost_stok_id' => $lossStok->id,
                    'barang_id' => $barang->id,
                ]);

                $barang->update([
                    'status' => 'Loss',
                ]);

                $loss->delete();

                DB::commit();

                return back()->with('success', 'Barang berhasil dipindahkan');
            } else {
                return back()->with('error', "Data loss stok {$locator->nama_locator} tidak ditemukan");
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', $th->getMessage());
        }
    }
}
