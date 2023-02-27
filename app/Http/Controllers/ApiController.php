<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\DetailStokOpname;
use App\Models\Locator;
use App\Models\Satuan;
use App\Models\StokOpname;
use App\Models\TipeBarang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApiController extends Controller
{
    public function cekTag(Request $request)
    {
        $barang = Barang::where('rfid', $request->tag)->first();

        if ($barang) {
            return response()->json([
                'status' => 'error',
                'message' => 'not available'
            ]);
        } else {
            return response()->json([
                'status' => 'success',
                'message' => 'available'
            ]);
        }
    }

    public function getMaster()
    {
        $response = [
            'satuan' => Satuan::get(),
            'tipe' => TipeBarang::get(),
            'locator' => Locator::get(),
        ];

        return response()->json([
            'status' => 'success',
            'data' => $response
        ]);
    }

    public function create(Request $request)
    {
        try {
            DB::beginTransaction();

            $barang = Barang::create([
                'rfid' => $request->tag,
                'nama_barang' => $request->nama_barang,
                'harga' => $request->harga,
                'berat' => $request->berat,
                'satuan_id' => $request->satuan,
                'tipe_barang_id' => $request->tipe,
                'locator_id' => $request->locator,
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => "{$barang->nama_barang} berhasil ditambahkan"
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage()
            ]);
        }
    }

    public function stok(Request $request)
    {
        $request->validate([
            'tag' => 'required|array'
        ]);

        try {
            DB::beginTransaction();

            $stokOn = StokOpname::where('status', 1)->first();

            if ($stokOn) {
                foreach ($request->tag as $key => $val) {
                    $barang = Barang::where('rfid', $val)->first();

                    if ($barang && $barang->locator_id == $stokOn->locator_id) {
                        DetailStokOpname::updateOrCreate([
                            'stok_opname_id' => $stokOn->id,
                            'barang_id' => $barang->id
                        ]);
                    }
                }

                DB::commit();

                return response()->json([
                    'status' => 'success',
                    'message' => 'Stok berhasil diinput'
                ], 200);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Tidak ada stok yg aktif'
                ], 200);
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage()
            ], 200);
        }
    }
}
