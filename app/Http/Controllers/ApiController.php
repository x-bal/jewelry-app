<?php

namespace App\Http\Controllers;

use App\Models\Alarm;
use App\Models\Barang;
use App\Models\Device;
use App\Models\Locator;
use App\Models\LostStok;
use App\Models\Penarikan;
use App\Models\Penjualan;
use App\Models\Satuan;
use App\Models\Setting;
use App\Models\StokOpname;
use App\Models\SubTipeBarang;
use App\Models\TipeBarang;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

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

    public function cekrfid(Request $request)
    {
        try {
            DB::beginTransaction();

            $barang = Barang::where(['rfid' => $request->rfid, 'status' => 'Tersedia'])->first();

            if ($barang) {
                return response()->json([
                    'status' => 'success',
                    'barang' => $barang,
                    'locators' => Locator::get()
                ]);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Barang tidak ditemukan'
                ]);
            }

            DB::commit();
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage()
            ]);
        }
    }

    public function updateBarang(Request $request)
    {
        try {
            DB::beginTransaction();

            $barang = Barang::where(['id' => $request->idbarang, 'status' => 'Tersedia'])->first();

            if ($barang) {
                $barang->update([
                    'rfid' => $request->rfid,
                    'locator_id' => $request->locator
                ]);

                DB::commit();

                return response()->json([
                    'status' => 'success',
                    'message' => 'Barang berhasil diupdate'
                ]);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Barang tidak ditemukan'
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage()
            ]);
        }
    }

    public function getMaster()
    {
        $response = [
            'tipe' => TipeBarang::with('subs')->get(),
            'locator' => Locator::get(),
        ];

        return response()->json([
            'status' => 'success',
            'data' => $response
        ]);
    }

    public function sub(Request $request)
    {
        $tipeBarang = Barang::where(['sub_tipe_barang_id' => $request->id])->count();

        return response()->json([
            'status' => 'success',
            'last_id' => $tipeBarang ?? 0
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

            if ($request->file('foto')) {
                $foto = $request->file('foto');
                $fotoUrl = $foto->storeAs('barang', date('dmy') . '-' . $request->kode_barang . '.' . $foto->extension());
            } else {
                $fotoUrl = null;
            }

            $sub = SubTipeBarang::find($request->subtipe);

            $barang = Barang::create([
                'rfid' => $request->tag,
                'kode_barang' => $sub->kode,
                'nama_barang' => $request->nama_barang,
                'harga' => $request->harga,
                'berat' => $request->berat,
                'satuan' => 'Gram',
                'sub_tipe_barang_id' => $request->subtipe,
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
                'message' => 'Barang success di keranjang jual'
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage()
            ], 200);
        }
    }

    public function remove(Request $request)
    {
        $request->validate([
            'tag' => 'required|array'
        ]);

        try {
            DB::beginTransaction();

            $now = Carbon::now()->format('Y-m-d');

            foreach ($request->tag as $key => $val) {
                $barang = Barang::where(['rfid' => $val, 'status' => 'Tersedia'])->first();

                if ($barang) {
                    $penarikan = Penarikan::where(['tanggal' => $now, 'locator_id' => $barang->locator_id])->first();

                    DB::table('barang_penarikan')->updateOrInsert([
                        'barang_id' => $barang->id,
                        'penarikan_id' => $penarikan->id,
                        'ket' => 'Barang Lama'
                    ]);

                    $barang->update([
                        'status' => 'Ditarik',
                        'old_rfid' => $barang->rfid,
                    ]);

                    $barang->update(['rfid' => null]);
                }
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Barang success ditarik'
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage()
            ], 200);
        }
    }

    public function import(Request $request)
    {
        try {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');

            $tableNames = Schema::getConnection()->getDoctrineSchemaManager()->listTableNames();

            foreach ($tableNames as $name) {
                Schema::drop($name);
            }


            $sql = $request->file('sqlfile');
            $sqlUrl = $sql->storeAs('db', $sql->getClientOriginalName());
            $db = Storage::get($sqlUrl);

            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            DB::connection('mysql')->unprepared($db);

            return response()->json([
                'status' => 'success'
            ], 200);
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }

    public function alarm(Request $request)
    {
        try {
            DB::beginTransaction();

            foreach ($request->tag as $key => $val) {
                $barang = Barang::where(['rfid' => $val, 'status' => 'Tersedia'])->first();

                if ($barang) {
                    Alarm::updateOrCreate([
                        'barang_id' => $barang->id
                    ]);

                    $barang->update([
                        'status' => 'Hilang',
                        'old_rfid' => $barang->rfid,
                    ]);

                    $barang->update(['rfid' => null]);

                    DB::commit();

                    return response()->json([
                        'status' => 'success',
                        'message' => 'Barang berhasil didetect'
                    ]);
                } else {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Barang tidak ditemukan'
                    ]);
                }
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage()
            ]);
        }
    }

    public function alert(Request $request)
    {
        $lossing = Alarm::count();

        return response()->json([
            'status' => 'success',
            'lossing' => $lossing
        ]);
    }
}
