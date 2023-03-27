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
use App\Models\SubTipeBarang;
use App\Models\TipeBarang;
use App\Models\User;
use Carbon\Carbon;
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
        $tipeBarang = Barang::where(['sub_tipe_barang_id' => $request->id])->latest()->first();

        return response()->json([
            'status' => 'success',
            'last_id' => $tipeBarang->id ?? 0
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

            // Data Tipe
            $types = TipeBarang::where('is_sync', 0)->get();
            $nama_tipe = TipeBarang::where('is_sync', 0)->pluck('nama_tipe');
            $kode_tipe = TipeBarang::where('is_sync', 0)->pluck('kode');

            // Data Device
            $devices = Device::where('is_sync', 0)->get();
            $nama_device = Device::where('is_sync', 0)->pluck('nama_device');

            // Data Barang
            $barangs = Barang::where('is_sync', 0)->get();
            $nama_barang = Barang::where('is_sync', 0)->pluck('nama_barang');
            $satuan = Barang::where('is_sync', 0)->pluck('satuan');
            $tipe_barang_id = Barang::where('is_sync', 0)->pluck('tipe_barang_id');
            $locator_id = Barang::where('is_sync', 0)->pluck('locator_id');
            $rfid = Barang::where('is_sync', 0)->pluck('rfid');
            $kode_barang = Barang::where('is_sync', 0)->pluck('kode_barang');
            $berat = Barang::where('is_sync', 0)->pluck('berat');
            $harga = Barang::where('is_sync', 0)->pluck('harga');
            $status = Barang::where('is_sync', 0)->pluck('status');
            $old_rfid = Barang::where('is_sync', 0)->pluck('old_rfid');

            // Data Stok Opname
            $opnames = StokOpname::where('is_sync', 0)->get();
            $opname_locator = StokOpname::where('is_sync', 0)->pluck('locator_id');
            $opname_tanggal = StokOpname::where('is_sync', 0)->pluck('tanggal');
            $opname_status = StokOpname::where('is_sync', 0)->pluck('status');

            // Data Loss Stok
            $loss = LostStok::where('is_sync', 0)->get();
            $loss_locator = LostStok::where('is_sync', 0)->pluck('locator_id');
            $loss_tanggal = LostStok::where('is_sync', 0)->pluck('tanggal');

            // Data Penarikan
            $penarikans = Penarikan::where('is_sync', 0)->get();
            $penarikan_locator = Penarikan::where('is_sync', 0)->pluck('locator_id');
            $penarikan_tanggal = Penarikan::where('is_sync', 0)->pluck('tanggal');

            // Data Penjualan
            $penjualans = Penjualan::where('is_sync', 0)->get();
            $penjualan_user = Penjualan::where('is_sync', 0)->pluck('user_id');
            $penjualan_invoice = Penjualan::where('is_sync', 0)->pluck('invoice');
            $penjualan_tanggal = Penjualan::where('is_sync', 0)->pluck('tanggal');
            $penjualan_status = Penjualan::where('is_sync', 0)->pluck('status');

            // Barang Stok Opname
            $barangOpnames = DB::table('barang_stok_opname')->where('is_sync', 0)->get();
            $opname_brg = DB::table('barang_stok_opname')->where('is_sync', 0)->pluck('barang_id');
            $opname_id = DB::table('barang_stok_opname')->where('is_sync', 0)->pluck('stok_opname_id');

            // Barang Loss Stok
            $barangLoss = DB::table('barang_lost_stok')->where('is_sync', 0)->get();
            $loss_brg = DB::table('barang_lost_stok')->where('is_sync', 0)->pluck('barang_id');
            $loss_id = DB::table('barang_lost_stok')->where('is_sync', 0)->pluck('lost_stok_id');
            $loss_ket = DB::table('barang_lost_stok')->where('is_sync', 0)->pluck('ket');

            // Barang Penarikan
            $barangPenarikan = DB::table('barang_penarikan')->where('is_sync', 0)->get();
            $penarikan_brg = DB::table('barang_penarikan')->where('is_sync', 0)->pluck('barang_id');
            $penarikan_id = DB::table('barang_penarikan')->where('is_sync', 0)->pluck('penarikan_id');
            $penarikan_ket = DB::table('barang_penarikan')->where('is_sync', 0)->pluck('ket');

            // Barang Stok Opname
            $barangPenjualan = DB::table('barang_penjualan')->where('is_sync', 0)->get();
            $penjualan_brg = DB::table('barang_penjualan')->where('is_sync', 0)->pluck('barang_id');
            $penjualan_id = DB::table('barang_penjualan')->where('is_sync', 0)->pluck('penjualan_id');

            // Device User
            $deviceUser = DB::table('device_user')->where('is_sync', 0)->get();
            $device_id = DB::table('device_user')->where('is_sync', 0)->pluck('device_id');
            $device_user = DB::table('device_user')->where('is_sync', 0)->pluck('user_id');

            // Setting
            $settings = Setting::get();
            $setting_name = Setting::pluck('name');
            $setting_val = Setting::pluck('val');

            $dataSend = [
                // User
                'username' => $username,
                'name' => $name,
                'password' => $password,
                // Locator
                'nama_locator' => $nama_locator,
                // Tipe
                'nama_tipe' => $nama_tipe,
                'kode_tipe' => $kode_tipe,
                // Device
                'nama_device' => $nama_device,
                // Barang
                'satuan' => $satuan,
                'tipe_barang_id' => $tipe_barang_id,
                'locator_id' => $locator_id,
                'rfid' => $rfid,
                'kode_barang' => $kode_barang,
                'nama_barang' => $nama_barang,
                'berat' => $berat,
                'harga' => $harga,
                'status' => $status,
                'old_rfid' => $old_rfid,
                // Stok Opname
                'opname_locator' => $opname_locator,
                'opname_tanggal' => $opname_tanggal,
                'opname_status' => $opname_status,
                // Loss
                'loss_locator' => $loss_locator,
                'loss_tanggal' => $loss_tanggal,
                // Penarikan
                'penarikan_locator' => $penarikan_locator,
                'penarikan_tanggal' => $penarikan_tanggal,
                // Penjualan
                'penjualan_user' => $penjualan_user,
                'penjualan_invoice' => $penjualan_invoice,
                'penjualan_tanggal' => $penjualan_tanggal,
                'penjualan_status' => $penjualan_status,
                // Barang Opname
                'opname_brg' => $opname_brg,
                'opname_id' => $opname_id,
                // Barang Loss
                'loss_brg' => $loss_brg,
                'loss_id' => $loss_id,
                'loss_ket' => $loss_ket,
                // Barang Penarikan
                'penarikan_brg' => $penarikan_brg,
                'penarikan_id' => $penarikan_id,
                'penarikan_ket' => $penarikan_ket,
                // Barang Penjualan
                'penjualan_brg' => $penjualan_brg,
                'penjualan_id' => $penjualan_id,
                // Device
                'device_id' => $device_id,
                'user_id' => $device_user,
                // Setting
                'setting_name' => $setting_name,
                'setting_val' => $setting_val,
            ];

            $send = Http::post($url, $dataSend);

            if ($send->status() == 200) {
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

                foreach ($opnames as $opname) {
                    $opname->update([
                        'is_sync' => 1
                    ]);
                }

                foreach ($loss as $los) {
                    $los->update([
                        'is_sync' => 1
                    ]);
                }

                foreach ($penarikans as $penarikan) {
                    $penarikan->update([
                        'is_sync' => 1
                    ]);
                }

                foreach ($penjualans as $penjualan) {
                    $penjualan->update([
                        'is_sync' => 1
                    ]);
                }

                foreach ($barangOpnames as $brgOp) {
                    $brgOp->update([
                        'is_sync' => 1
                    ]);
                }

                foreach ($barangLoss as $brgloss) {
                    $brgloss->update([
                        'is_sync' => 1
                    ]);
                }

                foreach ($barangPenarikan as $brgpnrk) {
                    $brgpnrk->update([
                        'is_sync' => 1
                    ]);
                }

                foreach ($barangPenjualan as $brgpnj) {
                    $brgpnj->update([
                        'is_sync' => 1
                    ]);
                }

                foreach ($deviceUser as $devuser) {
                    $devuser->update([
                        'is_sync' => 1
                    ]);
                }

                foreach ($settings as $setting) {
                    $setting->update([
                        'is_sync' => 1
                    ]);
                }

                DB::commit();

                return back()->with('success', "Sinkronisasi database berhasil");
            } else {
                return back()->with('error', $send->body());
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', $th->getMessage());
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
                    'satuan' => $request->satuan[$key],
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
