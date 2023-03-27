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
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function __construct()
    {
        $this->middleware(['can:setting-access']);
    }

    public function index()
    {
        $title = 'Setting';
        $breadcrumbs = ['Setting',];
        $setting = Setting::first();
        $url = Setting::where('name', 'url')->first();
        $tagline = Setting::where('name', 'tagline')->first();

        return view('setting.index', compact('title', 'breadcrumbs', 'setting', 'url', 'tagline'));
    }

    public function update(Request $request)
    {
        try {
            DB::beginTransaction();

            $title = Setting::where('name', 'title')->first();

            $title->update([
                'val' => $request->title
            ]);

            $tagline = Setting::where('name', 'tagline')->first();

            $tagline->update([
                'val' => $request->tagline
            ]);

            $url = Setting::where('name', 'url')->first();

            $url->update([
                'val' => $request->url
            ]);

            $bg = Setting::where('name', 'bg')->first();

            if ($request->file('foto')) {
                $bg ? Storage::delete($bg->foto) : '';
                $foto = $request->file('foto');
                $fotoUrl = $foto->storeAs('background', 'login-bg.' . $foto->extension());
            } else {
                $fotoUrl = $bg->val;
            }

            $bg->update(['val' => $fotoUrl]);

            DB::commit();

            return back()->with('success', 'Setting berhasil diupdate');
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', $th->getMessage());
        }
    }

    public function export(Request $request)
    {
        //ENTER THE RELEVANT INFO BELOW
        $mysqlHostName      = env('DB_HOST');
        $mysqlUserName      = env('DB_USERNAME');
        $mysqlPassword      = env('DB_PASSWORD');
        $DbName             = env('DB_DATABASE');
        $file_name = 'databasebackup.sql';

        $tables = ['devices', 'dummy_barangs', 'failed_jobs', 'locators', 'lost_stoks', 'migrations', 'penarikans', 'permissions', 'personal_access_tokens', 'roles', 'settings', 'stok_opnames', 'tipe_barangs', 'sub_tipe_barangs', 'users', 'penjualans', 'barangs', 'barang_lost_stok', 'barang_penarikan', 'barang_penjualan', 'barang_stok_opname', 'device_user', 'model_has_permissions', 'model_has_roles', 'password_resets', 'role_has_permissions'];

        $connect = new \PDO("mysql:host=$mysqlHostName;dbname=$DbName;charset=utf8", "$mysqlUserName", "$mysqlPassword", array(\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
        $get_all_table_query = "SHOW TABLES";
        $statement = $connect->prepare($get_all_table_query);
        $statement->execute();
        $result = $statement->fetchAll();
        $output = '';
        foreach ($tables as $table) {
            $show_table_query = "SHOW CREATE TABLE " . $table . "";
            $statement = $connect->prepare($show_table_query);
            $statement->execute();
            $show_table_result = $statement->fetchAll();

            foreach ($show_table_result as $show_table_row) {
                $output .= "\n\n" . $show_table_row["Create Table"] . ";\n\n";
            }
            $select_query = "SELECT * FROM " . $table . "";
            $statement = $connect->prepare($select_query);
            $statement->execute();
            $total_row = $statement->rowCount();

            for ($count = 0; $count < $total_row; $count++) {
                $single_result = $statement->fetch(\PDO::FETCH_ASSOC);
                $table_column_array = array_keys($single_result);
                $table_value_array = array_values($single_result);
                $output .= "\nINSERT INTO $table (";
                $output .= "" . implode(", ", $table_column_array) . ") VALUES (";
                $output .= "'" . implode("','", $table_value_array) . "');\n";
            }
        }

        $file_handle = fopen($file_name, 'w+');
        fwrite($file_handle, $output);
        fclose($file_handle);

        $sqlfile = file_get_contents($file_name);

        $setting = Setting::where('name', 'url')->first()->val;
        $url = $setting . '/api/import';

        $send = Http::attach('sqlfile', file_get_contents($file_name), 'sqlfile.sql')
            ->post($url, [
                'sqlfile' => $sqlfile
            ]);

        if ($send->status() == 200) {
            return back()->with('success', 'Sync database berhasil');
        } else {
            return $send->body();
            return back()->with('error', $send->body());
        }
    }
}
