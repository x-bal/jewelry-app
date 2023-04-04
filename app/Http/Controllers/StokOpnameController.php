<?php

namespace App\Http\Controllers;

use App\Http\Requests\StokOpnameRequest;
use App\Models\Barang;
use App\Models\DetailLostStok;
use App\Models\Locator;
use App\Models\LostStok;
use App\Models\StokOpname;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;

class StokOpnameController extends Controller
{
    public function __construct()
    {
        $this->middleware(['can:inventory-access'])->except('stok', 'unstock');
    }

    public function index()
    {
        $title = 'Data Stok Opname';
        $breadcrumbs = ['Stok Opname'];
        $locator = Locator::get();

        return view('stok-opname.index', compact('title', 'breadcrumbs', 'locator'));
    }

    public function get(Request $request)
    {
        if ($request->ajax()) {
            $data = StokOpname::whereDate('created_at', $request->tanggal)->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $actionBtn = '<a href="' . route('stok-opname.show', $row->id) . '" id="' . $row->id . '" class="btn btn-sm btn-info">Input Stok</a> <a href="#modal-dialog" id="' . $row->id . '" class="btn btn-sm btn-success btn-edit" data-route="' . route('stok-opname.update', $row->id) . '" data-bs-toggle="modal">Edit</a> <button type="button" data-route="' . route('stok-opname.destroy', $row->id) . '" class="delete btn btn-danger btn-delete btn-sm">Delete</button>';
                    return $actionBtn;
                })
                ->editColumn('status', function ($row) {
                    if ($row->status == 1) {
                        $checked = 'checked';
                    } else {
                        $checked = '';
                    }
                    return '<div class="form-check form-switch"><input class="form-check-input check-running" type="checkbox" id="switch" data-id="' . $row->id . '" ' . $checked . '></div>';
                })
                ->editColumn('tanggal', function ($row) {
                    return Carbon::parse($row->tanggal)->format('d/m/Y');
                })
                ->addColumn('locator', function ($row) {
                    return $row->locator->nama_locator;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }
    }

    public function store(StokOpnameRequest $stokOpnameRequest)
    {
        try {
            DB::beginTransaction();

            $stok = StokOpname::create([
                'tanggal' => $stokOpnameRequest->tanggal,
                'locator_id' => $stokOpnameRequest->locator,
            ]);

            DB::commit();

            return back()->with('success', "Stok tanggal {$stok->tanggal} berhasil ditambahkan");
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }

    public function show(StokOpname $stokOpname)
    {
        $title = 'Input Stok Opname';
        $breadcrumbs = ['Stok Opname', 'Input'];
        $barangId = $stokOpname->barangs()->pluck('barang_id');
        $barangs = Barang::whereNotIn('id', $barangId)->where(['status' => 'Tersedia', 'locator_id' => $stokOpname->locator_id])->get();

        return view('stok-opname.show', compact('title', 'breadcrumbs', 'stokOpname', 'barangs'));
    }

    public function find(StokOpname $stokOpname)
    {
        return response()->json([
            'status' => 'success',
            'stok' => $stokOpname
        ], 200);
    }

    public function update(Request $request, StokOpname $stokOpname)
    {
        try {
            DB::beginTransaction();

            foreach ($stokOpname->barangs as $barang) {
                $barang->update([
                    'rfid' => $barang->old_rfid,
                    'status' => 'Tersedia'
                ]);

                $barang->update([
                    'old_rfid' => null
                ]);
            }

            $stokOpname->update([
                'tanggal' => $request->tanggal,
                'locator_id' => $request->locator,
            ]);

            DB::commit();

            return back()->with('success', "Stok opname berhasil diupdate");
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }

    public function change(Request $request)
    {
        try {
            DB::beginTransaction();

            $stock = StokOpname::find($request->id);

            $stocks = StokOpname::where('id', '!=', $stock->id)->get();

            foreach ($stocks as $stok) {
                $stok->update(['status' => 0]);
            }

            $stock->update(['status' => $request->status]);

            if ($stock->status == 1) {
                $status = 'On';
            } else {
                $status = 'Off';
            }

            DB::commit();

            return response()->json([
                'status' => $status
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function stok(StokOpname $stokOpname)
    {
        if (request()->ajax()) {

            return DataTables::of($stokOpname->barangs)
                ->addIndexColumn()
                ->addColumn('barang', function ($row) {
                    return $row->nama_barang;
                })
                ->addColumn('rfid', function ($row) {
                    return $row->rfid != null ? $row->rfid : $row->old_rfid;
                })
                ->addColumn('action', function ($row) use ($stokOpname) {
                    $actionBtn = '<a href="#modal-detail" id="' . $row->id . '" class="btn btn-xs btn-info btn-show" data-bs-toggle="modal"><i class="ion-ios-eye"></i></a> <a href="' . route('stok-opname.remove', $row->id) . '?stokopname=' . $stokOpname->id . '" id="' . $row->id . '" class="btn btn-xs btn-danger btn-remove"><i class="ion-ios-remove"></i></a>';
                    return $actionBtn;
                })
                ->rawColumns(['barang', 'rfid', 'action'])
                ->make(true);
        }
    }

    public function unstock(StokOpname $stokOpname)
    {
        if (request()->ajax()) {
            $locator = Locator::find($stokOpname->locator_id);
            $stokBarang = array_column($stokOpname->barangs()->get()->toArray(), 'id');

            $barang = $locator->barangs()->whereNotIn('id', $stokBarang)->get();

            return DataTables::of($barang)
                ->addIndexColumn()
                ->addColumn('action', function ($row) use ($stokOpname) {
                    $actionBtn = '<a href="#modal-detail" id="' . $row->id . '" class="btn btn-xs btn-info btn-show" data-bs-toggle="modal"><i class="ion-ios-eye"></i></a> <a href="' . route('stok-opname.add', $row->id) . '?stokopname=' . $stokOpname->id . '" id="' . $row->id . '" class="btn btn-xs btn-primary btn-add"><i class="ion-ios-add"></i></a>';
                    return $actionBtn;
                })
                ->addColumn('rfid', function ($row) {
                    return $row->rfid != null ? Str::limit($row->rfid, 8) : Str::limit($row->old_rfid, 8);
                })
                ->addColumn('barang', function ($row) {
                    return $row->nama_barang ?? '';
                })
                ->rawColumns(['barang', 'rfid', 'action'])
                ->make(true);
        }
    }

    public function add(Request $request, Barang $barang)
    {
        try {
            DB::beginTransaction();

            $stokOpname = StokOpname::find($request->stokopname);

            $lost = DB::table('barang_lost_stok')->where('barang_id', $barang->id)->first();
            $lostStok = LostStok::where(['tanggal' => $stokOpname->tanggal, 'locator_id' => $stokOpname->locator_id])->first();
            $lostStok->barangs()->detach($barang->id);

            if ($barang->rfid != null) {
                $barang->update([
                    'status' => 'Tersedia',
                    'rfid' => $barang->rfid,
                ]);

                $barang->update(['old_rfid' => null]);
            } else {
                $barang->update([
                    'status' => 'Tersedia',
                    'rfid' => $barang->old_rfid,
                ]);

                $barang->update(['old_rfid' => null]);
            }

            DB::table('barang_stok_opname')->updateOrInsert([
                'stok_opname_id' => $stokOpname->id,
                'barang_id' => $barang->id
            ]);

            DB::commit();

            return back()->with('success', "Barang berhasil ditambahkan ke stok");
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', $th->getMessage());
        }
    }

    public function remove(Request $request, Barang $barang)
    {
        try {
            DB::beginTransaction();

            $stokOpname = StokOpname::find($request->stokopname);
            $stokOpname->barangs()->detach($barang->id);
            $lost = LostStok::where(['tanggal' => $stokOpname->tanggal, 'locator_id' => $stokOpname->locator_id])->first();

            if ($barang->rfid != null) {
                $barang->update([
                    'status' => 'Loss',
                    'old_rfid' => $barang->rfid,
                ]);

                $barang->update(['rfid' => null]);
            } else {
                $barang->update([
                    'status' => 'Loss',
                    'old_rfid' => $barang->old_rfid,
                ]);

                $barang->update(['rfid' => null]);
            }

            DB::table('barang_lost_stok')->updateOrInsert([
                'lost_stok_id' => $lost->id,
                'barang_id' => $barang->id
            ]);

            DB::commit();

            return back()->with('success', "Barang berhasil diremove dari stok");
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', $th->getMessage());
        }
    }

    public function save(StokOpname $stokOpname)
    {
        try {
            DB::beginTransaction();

            $locator = Locator::find($stokOpname->locator_id);
            $stokBarang = array_column($stokOpname->barangs()->get()->toArray(), 'id');

            $barangs = $locator->barangs()->whereNotIn('id', $stokBarang)->get();

            $lostStok = LostStok::where([
                'tanggal' => $stokOpname->tanggal,
                'locator_id' => $stokOpname->locator_id
            ])->first();

            if (!$lostStok) {
                $lost = LostStok::create([
                    'tanggal' => $stokOpname->tanggal,
                    'locator_id' => $stokOpname->locator_id
                ]);

                foreach ($barangs as $barang) {
                    if ($barang->rfid != null) {
                        $barang->update([
                            'status' => 'Loss',
                            'old_rfid' => $barang->rfid,
                        ]);

                        $barang->update(['rfid' => null,]);
                    }

                    DB::table('barang_lost_stok')->updateOrInsert(
                        [
                            'lost_stok_id' => $lost->id
                        ],
                        [
                            'barang_id' => $barang->id,
                            'ket' => 'Loss'
                        ]
                    );
                }
            } else {
                $lost = LostStok::where([
                    'tanggal' => $stokOpname->tanggal,
                    'locator_id' => $stokOpname->locator_id
                ])->first();

                foreach ($barangs as $barang) {
                    if ($barang->rfid != null) {
                        $barang->update([
                            'status' => 'Loss',
                            'old_rfid' => $barang->rfid,
                        ]);

                        $barang->update(['rfid' => null,]);
                    }

                    DB::table('barang_lost_stok')->updateOrInsert(
                        [
                            'lost_stok_id' => $lost->id,
                            'barang_id' => $barang->id,
                        ],
                        [
                            'ket' => 'Loss'
                        ]
                    );
                }
            }

            DB::commit();

            return redirect()->route('stok-opname.index')->with('success', "Stok opname tanggal {$stokOpname->tanggal} berhasil disimpan");
        } catch (\Throwable $th) {
            DB::rollBack();
            return $th->getMessage();
            return back()->with('error', $th->getMessage());
        }
    }

    public function destroy(StokOpname $stokOpname)
    {
        try {
            DB::beginTransaction();
            foreach ($stokOpname->barangs as $barang) {
                $barang->update([
                    'rfid' => $barang->old_rfid,
                    'status' => 'Tersedia'
                ]);

                $barang->update([
                    'old_rfid' => null
                ]);
            }

            $stokOpname->barangs()->detach();

            $stokOpname->delete();

            DB::commit();

            return back()->with('success', "Stok opname berhasil didelete");
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }

    public function view(StokOpname $stokOpname)
    {
        try {
            DB::beginTransaction();

            $barangs = $stokOpname->barangs()->pluck('id');

            DB::commit();

            return response()->json([
                'status' => 'success',
                'barang' => $barangs
            ]);
        } catch (\Throwable $th) {
            //throw $th;
        }
    }
}
