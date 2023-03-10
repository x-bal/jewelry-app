<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Device;
use App\Models\Locator;
use App\Models\LostStok;
use App\Models\Penarikan;
use App\Models\Penjualan;
use App\Models\Satuan;
use App\Models\Setting;
use App\Models\StokOpname;
use App\Models\TipeBarang;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class ApiController extends Controller
{
    public function cekTag(Request $request)
    {
        $barang = Barang::where(['rfid' => $request->tag, 'status' => 'Tersedia'])->first();

        if ($barang) {
            return response()->json([
                'status' => 'error',
                'message' => 'Not available'
            ]);
        } else {
            return response()->json([
                'status' => 'success',
                'message' => 'Available'
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

    public function lastBarang()
    {
        return response()->json([
            'status' => 'success',
            'last_id' => Barang::latest()->first()->id ?? 0
        ]);
    }

    public function create(Request $request)
    {
        try {
            DB::beginTransaction();

            $foto = $request->file('foto');
            $fotoUrl = $foto->storeAs('barang', date('dmy') . '-' . $request->kode_barang . '.' . $foto->extension());

            $barang = Barang::create([
                'rfid' => $request->tag,
                'kode_barang' => $request->kode_barang,
                'nama_barang' => $request->nama_barang,
                'harga' => $request->harga,
                'berat' => $request->berat,
                'satuan_id' => $request->satuan,
                'tipe_barang_id' => $request->tipe,
                'locator_id' => $request->locator,
                'foto' => $fotoUrl
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
                    $barang = Barang::where(['rfid' => $val, 'status' => 'Tersedia'])->first();

                    if ($barang && $barang->locator_id == $stokOn->locator_id) {
                        DB::table('barang_stok_opname')->updateOrInsert([
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

    public function sale(Request $request)
    {
        $request->validate([
            'iddev' => 'required|numeric',
            'tag' => 'required|array'
        ]);

        try {
            DB::beginTransaction();

            $device = DB::table('device_user')->where('device_id', $request->iddev)->first();
            $penjualan = Penjualan::where(['user_id' => $device->user_id, 'status' => 'Input'])->latest()->first();

            foreach ($request->tag as $key => $val) {
                $barang = Barang::where(['rfid' => $val, 'status' => 'Tersedia'])->first();

                if ($barang) {
                    DB::table('barang_penjualan')->updateOrInsert([
                        'penjualan_id' => $penjualan->id,
                        'barang_id' => $barang->id
                    ]);

                    $barang->update([
                        'status' => 'Terjual',
                        'old_rfid' => $barang->rfid,
                    ]);

                    $barang->update(['rfid' => null]);
                }
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'berhasil' => 'Barang success dijual'
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage()
            ], 200);
        }
    }

    public function receiveSync(Request $request)
    {
        try {
            DB::beginTransaction();

            foreach ($request->username as $key => $val) {
                User::create([
                    'username' => $request->username[$key],
                    'name' => $request->name[$key],
                    'password' => $request->password[$key],
                    'is_sync' => 1
                ]);
            }

            foreach ($request->nama_locator as $key => $val) {
                Locator::create([
                    'nama_locator' => $request->nama_locator[$key],
                    'is_sync' => 1
                ]);
            }

            foreach ($request->nama_satuan as $key => $val) {
                Satuan::create([
                    'nama_satuan' => $request->nama_satuan[$key],
                    'is_sync' => 1
                ]);
            }

            foreach ($request->nama_tipe as $key => $val) {
                TipeBarang::create([
                    'nama_tipe' => $request->nama_tipe[$key],
                    'kode' => $request->kode_tipe[$key],
                    'is_sync' => 1
                ]);
            }

            foreach ($request->nama_device as $key => $val) {
                Device::create([
                    'nama_device' => $request->nama_device[$key],
                    'is_sync' => 1
                ]);
            }

            foreach ($request->nama_barang as $key => $val) {
                Barang::create([
                    'nama_barang' => $request->nama_barang[$key],
                    'kode_barang' => $request->kode_barang[$key],
                    'satuan_id' => $request->satuan_id[$key],
                    'locator_id' => $request->locator_id[$key],
                    'tipe_barang_id' => $request->tipe_barang_id[$key],
                    'rfid' => $request->rfid[$key],
                    'berat' => $request->berat[$key],
                    'harga' => $request->harga[$key],
                    'status' => $request->status[$key],
                    'old_rfid' => $request->old_rfid[$key],
                    'is_sync' => 1
                ]);
            }

            foreach ($request->opname_locator as $key => $val) {
                StokOpname::create([
                    'locator_id' => $request->opname_locator[$key],
                    'tanggal' => $request->opname_tanggal[$key],
                    'status' => $request->opname_status[$key],
                    'is_sync' => 1
                ]);
            }

            foreach ($request->opname_locator as $key => $val) {
                StokOpname::create([
                    'locator_id' => $request->opname_locator[$key],
                    'tanggal' => $request->opname_tanggal[$key],
                    'status' => $request->opname_status[$key],
                    'is_sync' => 1
                ]);
            }

            foreach ($request->loss_locator as $key => $val) {
                LostStok::create([
                    'locator_id' => $request->loss_locator[$key],
                    'tanggal' => $request->loss_tanggal[$key],
                    'is_sync' => 1
                ]);
            }

            foreach ($request->penarikan_locator as $key => $val) {
                Penarikan::create([
                    'locator_id' => $request->penarikan_locator[$key],
                    'tanggal' => $request->penarikan_tanggal[$key],
                    'is_sync' => 1
                ]);
            }

            foreach ($request->penjualan_user as $key => $val) {
                Penjualan::create([
                    'user_id' => $request->penjualan_user[$key],
                    'tanggal' => $request->penjualan_tanggal[$key],
                    'invoice' => $request->penjualan_invoice[$key],
                    'status' => $request->penjualan_status[$key],
                    'is_sync' => 1
                ]);
            }

            foreach ($request->opname_brg as $key => $val) {
                DB::table('barang_stok_opname')->create([
                    'barang_id' => $request->opname_brg[$key],
                    'stok_opname_id' => $request->opname_id[$key],
                    'is_sync' => 1
                ]);
            }

            foreach ($request->loss_brg as $key => $val) {
                DB::table('barang_lost_stok')->create([
                    'barang_id' => $request->loss_brg[$key],
                    'lost_stok_id' => $request->loss_id[$key],
                    'ket' => $request->loss_ket[$key],
                    'is_sync' => 1
                ]);
            }

            foreach ($request->penarikan_brg as $key => $val) {
                DB::table('barang_penarikan')->create([
                    'barang_id' => $request->penarikan_brg[$key],
                    'penarikan_id' => $request->penarikan_id[$key],
                    'ket' => $request->penarikan_ket[$key],
                    'is_sync' => 1
                ]);
            }

            foreach ($request->penjualan_brg as $key => $val) {
                DB::table('barang_penjualan')->create([
                    'barang_id' => $request->penjualan_brg[$key],
                    'penjualan_id' => $request->penjualan_id[$key],
                    'is_sync' => 1
                ]);
            }

            foreach ($request->device_id as $key => $val) {
                DB::table('barang_penjualan')->create([
                    'device_id' => $request->device_id[$key],
                    'device_user' => $request->device_user[$key],
                    'is_sync' => 1
                ]);
            }

            foreach ($request->setting_name as $key => $val) {
                Setting::updateOrCreate([
                    'name' => $request->setting_name[$key],
                    'val' => $request->setting_val[$key],
                    'is_sync' => 1
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => "Success"
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => $th->getMessage()
            ]);
        }
    }
}
