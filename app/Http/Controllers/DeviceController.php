<?php

namespace App\Http\Controllers;

use App\Http\Requests\DeviceRequest;
use App\Models\Device;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class DeviceController extends Controller
{
    public function index()
    {
        $title = 'Data Device';
        $breadcrumbs = ['Master Device', 'Data Device'];
        $users = User::get();

        return view('device.index', compact('title', 'breadcrumbs', 'users'));
    }

    public function get(Request $request)
    {
        if ($request->ajax()) {
            $data = Device::get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $actionBtn = '<a href="#modal-pairing" id="' . $row->id . '" class="btn btn-sm btn-info btn-pairing" data-route="' . route('devices.pairing', $row->id) . '" data-bs-toggle="modal">Pairing</a> <a href="#modal-dialog" id="' . $row->id . '" class="btn btn-sm btn-success btn-edit" data-route="' . route('devices.update', $row->id) . '" data-bs-toggle="modal">Edit</a> <button type="button" data-route="' . route('devices.destroy', $row->id) . '" class="delete btn btn-danger btn-delete btn-sm">Delete</button>';
                    return $actionBtn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    public function store(DeviceRequest $deviceRequest)
    {
        try {
            DB::beginTransaction();

            $device = Device::create($deviceRequest->all());

            DB::commit();

            return redirect()->route('devices.index')->with('success', "{$device->nama_device} berhasil ditambahkan");
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', $th->getMessage());
        }
    }

    public function show(Device $device)
    {
        return response()->json([
            'status' => 'success',
            'device' => $device
        ]);
    }

    public function update(DeviceRequest $deviceRequest, Device $device)
    {
        try {
            DB::beginTransaction();

            $device->update($deviceRequest->all());

            DB::commit();

            return redirect()->route('devices.index')->with('success', "{$device->nama_device} berhasil diupdate");
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', $th->getMessage());
        }
    }

    public function destroy(Device $device)
    {
        try {
            DB::beginTransaction();

            $device->delete();

            DB::commit();

            return redirect()->route('devices.index')->with('success', "{$device->nama_device} berhasil dihapus");
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', $th->getMessage());
        }
    }

    public function pairing(Request $request, Device $device)
    {
        $request->validate([
            'user' => 'required|numeric'
        ]);

        try {
            DB::beginTransaction();

            $pairingDev = DB::table('device_user')->where(['device_id' => $device->id])->first();
            $pairingUser = DB::table('device_user')->where(['user_id' => $request->user])->first();

            if ($pairingDev || $pairingUser) {
                return back()->with('error', 'Device sudah dipairing');
            } else {

                return "no";
                DB::table('device_user')->insert(['device_id' => $device->id, 'user_id' => $request->user]);
            }

            DB::commit();

            return back()->with('success', 'Pairing device berhasil');
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', $th->getMessage());
        }
    }
}
