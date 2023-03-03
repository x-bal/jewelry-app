<?php

namespace App\Http\Controllers;

use App\Http\Requests\PenarikanRequest;
use App\Models\Barang;
use App\Models\Penarikan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class PenarikanController extends Controller
{
    public function index()
    {
        $title = 'Data Penarikan';
        $breadcrumbs = ['Penarikan', 'List Penarikan'];
        $barangs = Barang::where('status', 'Tersedia')->get();

        return view('penarikan.index', compact('title', 'breadcrumbs', 'barangs'));
    }

    public function get(Request $request)
    {
        if ($request->ajax()) {
            $data = Penarikan::get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('tanggal', function ($row) {
                    return Carbon::parse($row->tanggal)->format('d/m/Y');
                })
                ->addColumn('action', function ($row) {
                    $actionBtn = '<a href="' . route('penjualan.edit', $row->id) . '" id="' . $row->id . '" class="btn btn-sm btn-success ">Edit</a> <button type="button" data-route="' . route('penjualan.destroy', $row->id) . '" class="delete btn btn-danger btn-delete btn-sm">Delete</button>';
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

            $penarikan = Penarikan::create(['tanggal' => $penarikanRequest->tanggal]);

            $barang = Barang::find($penarikanRequest->barang);

            DB::table('barang_penarikan')->insert(['penarikan_id' => $penarikan->id, 'barang_id' => $barang->id]);

            $barang->update([
                'status' => 'Ditarik',
                'old_rfid' => $barang->rfid,
            ]);

            $barang->update(['rfid' => null]);


            DB::commit();

            return back()->with('success', "Penarikan barang berhasil");
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', $th->getMessage());
        }
    }

    public function show(Penarikan $penarikan)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Penarikan  $penarikan
     * @return \Illuminate\Http\Response
     */
    public function edit(Penarikan $penarikan)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Penarikan  $penarikan
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Penarikan $penarikan)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Penarikan  $penarikan
     * @return \Illuminate\Http\Response
     */
    public function destroy(Penarikan $penarikan)
    {
        //
    }
}
