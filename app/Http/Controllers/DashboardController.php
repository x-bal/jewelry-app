<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUserRequest;
use App\Models\Barang;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    public function index()
    {
        $title = 'Dashboard';
        $breadcrumbs = ['Dashboard'];
        $month = Carbon::now()->format('m');
        $year = Carbon::now()->format('Y');
        $totalBarang = Barang::whereMonth('created_at', $month)->whereYear('created_at', $year)->count();
        $totalLoss = Barang::whereMonth('created_at', $month)->whereYear('created_at', $year)->where('status', 'Loss')->count();
        $totalPenarikan = Barang::whereMonth('created_at', $month)->whereYear('created_at', $year)->where('status', 'Ditarik')->count();
        $totalPenjualan = Barang::whereMonth('created_at', $month)->whereYear('created_at', $year)->where('status', 'Terjual')->count();

        return view('dashboard.index', compact('title', 'breadcrumbs', 'totalBarang', 'totalLoss', 'totalPenarikan', 'totalPenjualan'));
    }

    public function profile()
    {
        $title = 'Profile';
        $breadcrumbs = ['Profile'];
        $user = User::find(auth()->user()->id);

        return view('dashboard.profile', compact('title', 'breadcrumbs', 'user'));
    }

    public function update(UpdateUserRequest $updateUserRequest, User $user)
    {
        try {
            DB::beginTransaction();

            if ($updateUserRequest->file('foto')) {
                Storage::delete($user->foto);
                $foto = $updateUserRequest->file('foto');
                $fotoUrl = $foto->storeAs('users', Str::slug(date('dmy') . '-' . $updateUserRequest->username) . '.' . $foto->extension());
            } else {
                $fotoUrl = $user->foto;
            }

            if ($updateUserRequest->password) {
                $password = bcrypt($updateUserRequest->password);
            } else {
                $password = $user->password;
            }

            $user->update([
                'username' => $updateUserRequest->username,
                'name' => $updateUserRequest->name,
                'password' => $password,
                'foto' => $fotoUrl,
            ]);

            DB::commit();

            return back()->with('success', "Profile {$user->name} berhasil diupdate");
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', $th->getMessage());
        }
    }
}
