<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\DataTables;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware(['can:master-access']);
    }

    public function index()
    {
        $title = 'Data User';
        $breadcrumbs = ['Master', 'Data User'];
        $roles = Role::get();

        return view('user.index', compact('title', 'breadcrumbs', 'roles'));
    }

    public function get(Request $request)
    {
        if ($request->ajax()) {
            $data = User::orderBy('name', 'asc')->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('role', function ($row) {
                    return $row->roles()->first()->name ?? '-';
                })
                ->addColumn('foto', function ($row) {
                    return $row->foto != null ? '<div class="menu-profile-image"><img src="' . asset('/storage/' . $row->foto) . '" alt="" width="35" class="rounded-circle"></div>' : '<div class="menu-profile-image"><img src="' . asset('/img/user/user-13.jpg') . '" alt="" width="35" class="rounded-circle"></div>';
                })
                ->addColumn('action', function ($row) {
                    $actionBtn = '<a href="#modal-dialog" id="' . $row->id . '" class="btn btn-sm btn-success btn-edit" data-route="' . route('users.update', $row->id) . '" data-bs-toggle="modal">Edit</a> <button type="button" data-route="' . route('users.destroy', $row->id) . '" class="delete btn btn-danger btn-delete btn-sm">Delete</button>';
                    return $actionBtn;
                })
                ->rawColumns(['action', 'role', 'foto'])
                ->make(true);
        }
    }

    public function store(CreateUserRequest $createUserRequest)
    {
        try {
            DB::beginTransaction();

            $foto = $createUserRequest->file('foto');
            $fotoUrl = $foto->storeAs('users', Str::slug(date('dmy') . '-' . $createUserRequest->username) . '.' . $foto->extension());

            $password = bcrypt($createUserRequest->password);

            $user = User::create([
                'username' => $createUserRequest->username,
                'name' => $createUserRequest->name,
                'password' => $password,
                'foto' => $fotoUrl,
            ]);

            $user->assignRole($createUserRequest->role);

            DB::commit();

            return redirect()->route('users.index')->with('success', "{$user->name} berhasil ditambahkan");
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', $th->getMessage());
        }
    }

    public function show(User $user)
    {
        return response()->json([
            'status' => 'success',
            'user' => $user,
            'role' => $user->roles()->first()
        ]);
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

            $user->assignRole($updateUserRequest->role);

            DB::commit();

            return redirect()->route('users.index')->with('success', "{$user->name} berhasil diupdate");
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', $th->getMessage());
        }
    }

    public function destroy(User $user)
    {
        try {
            DB::beginTransaction();

            $user->delete();

            DB::commit();

            return redirect()->route('users.index')->with('success', "{$user->name} berhasil dihapus");
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', $th->getMessage());
        }
    }
}
