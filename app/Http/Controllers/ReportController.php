<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Locator;
use App\Models\LostStok;
use App\Models\Penarikan;
use App\Models\StokOpname;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class ReportController extends Controller
{
    public function opname(Request $request)
    {
        $date = $request->from ? Carbon::parse($request->from)->format('d/m/Y') . ' s.d ' . Carbon::parse($request->to)->format('d/m/Y') : Carbon::now()->format('d/m/Y');

        $title = 'Report Stok Opname ' . $date;
        $breadcrumbs = ['Report', 'Stok Opname'];

        return view('report.opname', compact('title', 'breadcrumbs'));
    }

    public function listOpname(Request $request)
    {
        if ($request->ajax()) {
            $opnameId = StokOpname::with('barangs')->whereBetween('created_at', [$request->from, $request->to])->pluck('id');

            $data = DB::table('barang_stok_opname')->whereIn('stok_opname_id', $opnameId)->get();


            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('tanggal', function ($row) {
                    return Carbon::parse(StokOpname::find($row->stok_opname_id)->first()->tanggal)->format('d/m/Y') ?? '-';
                })
                ->editColumn('locator', function ($row) {
                    return Locator::find(StokOpname::find($row->stok_opname_id)->first()->locator_id)->nama_locator ?? '-';
                })
                ->editColumn('kode', function ($row) {
                    return Barang::find($row->barang_id)->kode_barang ?? '-';
                })
                ->editColumn('nama', function ($row) {
                    return Barang::find($row->barang_id)->nama_barang ?? '-';
                })
                ->editColumn('berat', function ($row) {
                    return Barang::find($row->barang_id)->berat . Barang::find($row->barang_id)->satuan->nama_satuan ?? '-';
                })
                ->editColumn('harga', function ($row) {
                    return 'Rp. ' . number_format(Barang::find($row->barang_id)->harga, 0, ',', '.') ?? '-';
                })
                ->rawColumns(['tanggal', 'locator'])
                ->make(true);
        }
    }

    public function loss(Request $request)
    {
        $date = $request->from ? Carbon::parse($request->from)->format('d/m/Y') . ' s.d ' . Carbon::parse($request->to)->format('d/m/Y') : Carbon::now()->format('d/m/Y');

        $title = 'Report Loss Stok ' . $date;
        $breadcrumbs = ['Report', 'Loss Stok'];

        return view('report.loss', compact('title', 'breadcrumbs'));
    }

    public function listLoss(Request $request)
    {
        if ($request->ajax()) {
            $lossId = LostStok::with('barangs')->whereBetween('created_at', [$request->from, $request->to])->pluck('id');

            $data = DB::table('barang_lost_stok')->whereIn('lost_stok_id', $lossId)->get();


            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('tanggal', function ($row) {
                    return Carbon::parse(LostStok::find($row->lost_stok_id)->first()->tanggal)->format('d/m/Y') ?? '-';
                })
                ->editColumn('locator', function ($row) {
                    return Locator::find(LostStok::find($row->lost_stok_id)->first()->locator_id)->nama_locator ?? '-';
                })
                ->editColumn('kode', function ($row) {
                    return Barang::find($row->barang_id)->kode_barang ?? '-';
                })
                ->editColumn('nama', function ($row) {
                    return Barang::find($row->barang_id)->nama_barang ?? '-';
                })
                ->editColumn('berat', function ($row) {
                    return Barang::find($row->barang_id)->berat . Barang::find($row->barang_id)->satuan->nama_satuan ?? '-';
                })
                ->editColumn('harga', function ($row) {
                    return 'Rp. ' . number_format(Barang::find($row->barang_id)->harga, 0, ',', '.') ?? '-';
                })
                ->rawColumns(['tanggal', 'locator'])
                ->make(true);
        }
    }

    public function penarikan(Request $request)
    {
        $date = $request->from ? Carbon::parse($request->from)->format('d/m/Y') . ' s.d ' . Carbon::parse($request->to)->format('d/m/Y') : Carbon::now()->format('d/m/Y');

        $title = 'Report Penarikan Barang ' . $date;
        $breadcrumbs = ['Report', 'Penarikan Barang'];

        return view('report.penarikan', compact('title', 'breadcrumbs'));
    }

    public function listPenarikan(Request $request)
    {
        if ($request->ajax()) {
            $penarikan = Penarikan::with('barangs')->whereBetween('created_at', [$request->from, $request->to])->pluck('id');

            $data = DB::table('barang_penarikan')->whereIn('penarikan_id', $penarikan)->get();


            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('tanggal', function ($row) {
                    return Carbon::parse(LostStok::find($row->penarikan_id)->first()->tanggal)->format('d/m/Y') ?? '-';
                })
                ->editColumn('locator', function ($row) {
                    return Locator::find(LostStok::find($row->penarikan_id)->first()->locator_id)->nama_locator ?? '-';
                })
                ->editColumn('kode', function ($row) {
                    return Barang::find($row->barang_id)->kode_barang ?? '-';
                })
                ->editColumn('nama', function ($row) {
                    return Barang::find($row->barang_id)->nama_barang ?? '-';
                })
                ->editColumn('ket', function ($row) {
                    return $row->ket ?? '-';
                })
                ->editColumn('berat', function ($row) {
                    return Barang::find($row->barang_id)->berat . Barang::find($row->barang_id)->satuan->nama_satuan ?? '-';
                })
                ->editColumn('harga', function ($row) {
                    return 'Rp. ' . number_format(Barang::find($row->barang_id)->harga, 0, ',', '.') ?? '-';
                })
                ->rawColumns(['tanggal', 'locator'])
                ->make(true);
        }
    }
}
