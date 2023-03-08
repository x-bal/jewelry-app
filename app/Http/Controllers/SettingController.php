<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SettingController extends Controller
{
    public function index()
    {
        $title = 'Setting';
        $breadcrumbs = ['Setting',];
        $setting = Setting::first();

        return view('setting.index', compact('title', 'breadcrumbs', 'setting',));
    }

    public function update(Request $request, Setting $setting)
    {
        try {
            DB::beginTransaction();

            $setting->update([
                'val' => $request->title
            ]);

            DB::commit();

            return back()->with('success', 'Title berhasil diupdate');
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', $th->getMessage());
        }
    }
}
