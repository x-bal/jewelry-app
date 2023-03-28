<?php

namespace App\Http\Controllers;

use App\Models\Alarm;
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
                    $actionBtn = '<a href="#modal-detail" id="' . $row->barang->id . '" class="btn btn-sm btn-info btn-show" data-bs-toggle="modal"><i class="ion-ios-eye"></i></a> <button type="button" data-route="' . route('loss.destroy', $row->id) . '" class="delete btn btn-danger btn-delete btn-sm">Remove</button>';
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
}
