<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Device;
use App\Models\Locator;
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

    public function sendSync(Request $request)
    {
        try {
            DB::beginTransaction();

            $setting = Setting::where('name', 'url')->first()->val;
            $url = $setting . '/api/receive-sync';

            $send = $this->sendDataSync($url);

            DB::commit();
            return $send;
        } catch (\Throwable $th) {
            DB::rollBack();
            return $th->getMessage();
        }
    }

    public function sendDataSync($url)
    {
        try {
            DB::beginTransaction();

            // Data User
            $users = User::where('is_sync', 0)->get();
            $username = User::where('is_sync', 0)->pluck('username');
            $name = User::where('is_sync', 0)->pluck('name');
            $password = User::where('is_sync', 0)->pluck('password');

            // Data Locator
            $locators = Locator::where('is_sync', 0)->get();
            $nama_locator = Locator::where('is_sync', 0)->pluck('nama_locator');

            // Data Satuan
            $satuans = Satuan::where('is_sync', 0)->get();
            $nama_satuan = Satuan::where('is_sync', 0)->pluck('nama_satuan');

            // Data Tipe
            $types = TipeBarang::where('is_sync', 0)->get();
            $nama_tipe = TipeBarang::where('is_sync', 0)->pluck('nama_tipe');

            // Data Device
            $devices = Device::where('is_sync', 0)->get();
            $nama_device = Device::where('is_sync', 0)->pluck('nama_device');

            // Data Barang
            $barangs = Barang::where('is_sync', 0)->get();
            $nama_barang = Barang::where('is_sync', 0)->pluck('nama_barang');
            $satuan_id = Barang::where('is_sync', 0)->pluck('satuan_id');
            $tipe_barang_id = Barang::where('is_sync', 0)->pluck('tipe_barang_id');
            $locator_id = Barang::where('is_sync', 0)->pluck('locator_id');
            $rfid = Barang::where('is_sync', 0)->pluck('rfid');
            $kode_barang = Barang::where('is_sync', 0)->pluck('kode_barang');
            $berat = Barang::where('is_sync', 0)->pluck('berat');
            $harga = Barang::where('is_sync', 0)->pluck('harga');
            $status = Barang::where('is_sync', 0)->pluck('status');
            $old_rfid = Barang::where('is_sync', 0)->pluck('old_rfid');

            $dataSend = [
                'username' => $username,
                'name' => $name,
                'password' => $password,
                'nama_locator' => $nama_locator,
                'nama_satuan' => $nama_satuan,
                'nama_tipe' => $nama_tipe,
                'nama_device' => $nama_device,
                'satuan_id' => $satuan_id,
                'tipe_barang_id' => $tipe_barang_id,
                'locator_id' => $locator_id,
                'rfid' => $rfid,
                'kode_barang' => $kode_barang,
                'nama_barang' => $nama_barang,
                'berat' => $berat,
                'harga' => $harga,
                'status' => $status,
                'old_rfid' => $old_rfid,
            ];

            $send = Http::post($url, $dataSend);

            foreach ($users as $user) {
                $user->update([
                    'is_sync' => 1
                ]);
            }

            foreach ($locators as $locator) {
                $locator->update([
                    'is_sync' => 1
                ]);
            }

            foreach ($satuans as $satuan) {
                $satuan->update([
                    'is_sync' => 1
                ]);
            }

            foreach ($types as $type) {
                $type->update([
                    'is_sync' => 1
                ]);
            }

            foreach ($devices as $device) {
                $device->update([
                    'is_sync' => 1
                ]);
            }

            foreach ($barangs as $barang) {
                $barang->update([
                    'is_sync' => 1
                ]);
            }

            DB::commit();

            return $send->body();
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => $th->getMessage()
            ]);
        }
    }

    public function receiveSync(Request $request)
    {
        return $request->all();
        foreach ($request->username as $key => $val) {
            User::create([
                'username' => $request->username[$key],
                'name' => $request->name[$key],
                'password' => $request->password[$key],
            ]);
        }
    }
}
