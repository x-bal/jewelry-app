<?php

use App\Http\Controllers\AlarmController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\DummyBarangController;
use App\Http\Controllers\LocatorController;
use App\Http\Controllers\LostStokController;
use App\Http\Controllers\PenarikanController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\StokOpnameController;
use App\Http\Controllers\SubTipeBarangController;
use App\Http\Controllers\TipeBarangController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ViewController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('auth.login');
});

Auth::routes();

Route::middleware(['auth'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('profile', [DashboardController::class, 'profile'])->name('profile');
    Route::put('profile/{user:id}', [DashboardController::class, 'update'])->name('profile.update');

    Route::get('users/get', [UserController::class, 'get'])->name('users.list');
    Route::resource('users', UserController::class);

    Route::get('permissions/get', [PermissionController::class, 'get'])->name('permissions.list');
    Route::resource('permissions', PermissionController::class);

    Route::get('roles/get', [RoleController::class, 'get'])->name('roles.list');
    Route::get('roles/{role:id}/find', [RoleController::class, 'find'])->name('roles.find');
    Route::resource('roles', RoleController::class);

    Route::get('locators/get', [LocatorController::class, 'get'])->name('locators.list');
    Route::resource('locators', LocatorController::class);

    Route::get('tipe-barang/get', [TipeBarangController::class, 'get'])->name('tipe-barang.list');
    Route::get('tipe-barang/{tipeBarang:id}detail', [TipeBarangController::class, 'detail'])->name('tipe-barang.detail');
    Route::get('/tipe-barang/sub', [TipeBarangController::class, 'sub'])->name('tipe-barang.sub');
    Route::resource('tipe-barang', TipeBarangController::class);
    Route::resource('sub-tipe-barang', SubTipeBarangController::class);

    Route::get('barang/get', [BarangController::class, 'get'])->name('barang.list');
    Route::post('barang/import', [BarangController::class, 'import'])->name('barang.import');
    Route::get('barang/download', [BarangController::class, 'download'])->name('barang.download');
    Route::resource('barang', BarangController::class);

    Route::get('dummy-barang/get', [DummyBarangController::class, 'get'])->name('dummy-barang.list');
    Route::resource('dummy-barang', DummyBarangController::class);

    Route::get('stok-opname/get', [StokOpnameController::class, 'get'])->name('stok-opname.list');
    Route::get('stok-opname/{stokOpname:id}/find', [StokOpnameController::class, 'find'])->name('stok-opname.find');
    Route::get('stok-opname/{stokOpname:id}/view', [StokOpnameController::class, 'view'])->name('stok-opname.view');
    Route::get('stok-opname/change', [StokOpnameController::class, 'change'])->name('stok-opname.change');
    Route::get('stok-opname/{stokOpname:id}/save', [StokOpnameController::class, 'save'])->name('stok-opname.save');
    Route::get('stok-opname/add/{barang:id}', [StokOpnameController::class, 'add'])->name('stok-opname.add');
    Route::get('stok-opname/remove/{barang:id}', [StokOpnameController::class, 'remove'])->name('stok-opname.remove');
    Route::post('stok-opname/addBarang', [StokOpnameController::class, 'addBarang'])->name('stok-opname.addbarang');
    Route::resource('stok-opname', StokOpnameController::class);

    Route::get('lost-stok/get', [LostStokController::class, 'get'])->name('lost-stok.list');
    Route::get('lost-stok/{lostStok:id}/lost', [LostStokController::class, 'lost'])->name('lost-stok.lost');
    Route::post('detail-lost/add-barang', [LostStokController::class, 'addBarang'])->name('detail-lost.add');
    Route::delete('detail-lost/{barang:id}/delete', [LostStokController::class, 'deleteBarang'])->name('detail-lost.destroy');
    Route::resource('lost-stok', LostStokController::class);

    Route::get('penjualan/get', [PenjualanController::class, 'get'])->name('penjualan.list');
    Route::get('penjualan/get-list/{penjualan:id}', [PenjualanController::class, 'getList'])->name('penjualan.get');
    Route::get('penjualan/view/{penjualan:id}', [PenjualanController::class, 'view'])->name('penjualan.view');
    Route::post('penjualan/add-barang', [PenjualanController::class, 'addBarang'])->name('penjualan.addbarang');
    Route::delete('detail-penjualan/{barang:id}/delete', [PenjualanController::class, 'deleteBarang'])->name('detail-penjualan.destroy');
    Route::resource('penjualan', PenjualanController::class);

    Route::get('devices/get', [DeviceController::class, 'get'])->name('devices.list');
    Route::post('devices/{device:id}/pairing', [DeviceController::class, 'pairing'])->name('devices.pairing');
    Route::resource('devices', DeviceController::class);

    Route::get('penarikan/get', [PenarikanController::class, 'get'])->name('penarikan.list');
    Route::get('penarikan/{penarikan:id}/get', [PenarikanController::class, 'getBarang'])->name('penarikan.get');
    Route::get('penarikan/{penarikan:id}/view', [PenarikanController::class, 'view'])->name('penarikan.view');
    Route::get('penarikan/change', [PenarikanController::class, 'change'])->name('penarikan.change');
    Route::post('detail-penarikan/add-barang', [PenarikanController::class, 'addBarang'])->name('detail-penarikan.add');
    Route::delete('detail-penarikan/{barang:id}/delete', [PenarikanController::class, 'deleteBarang'])->name('detail-penarikan.destroy');
    Route::resource('penarikan', PenarikanController::class);

    Route::get('loss/get', [AlarmController::class, 'get'])->name('loss.list');
    // Route::post('loss/add/{alarm:id}', [AlarmController::class, 'add'])->name('loss.add');
    Route::resource('loss', AlarmController::class);

    Route::get('setting', [SettingController::class, 'index'])->name('setting.index');
    Route::post('setting', [SettingController::class, 'update'])->name('setting.update');
    Route::get('send-sync', [SettingController::class, 'export'])->name('syncdb');

    Route::get('report/stok-opname', [ReportController::class, 'opname'])->name('report.opname');
    Route::get('report/list-opname', [ReportController::class, 'listOpname'])->name('report.opname.list');
    Route::get('report/loss-stok', [ReportController::class, 'loss'])->name('report.loss');
    Route::get('report/list-loss', [ReportController::class, 'listLoss'])->name('report.loss.list');
    Route::get('report/penarikan', [ReportController::class, 'penarikan'])->name('report.penarikan');
    Route::get('report/list-penarikan', [ReportController::class, 'listPenarikan'])->name('report.penarikan.list');
    Route::get('report/penjualan', [ReportController::class, 'penjualan'])->name('report.penjualan');
    Route::get('report/list-penjualan', [ReportController::class, 'listPenjualan'])->name('report.penjualan.list');
    Route::get('report/lossing', [ReportController::class, 'lossing'])->name('report.lossing');
    Route::get('report/list-lossing', [ReportController::class, 'listLossing'])->name('report.lossing.list');
});

Route::get('/view', ViewController::class);

Route::get('/exec', function () {
    // shell_exec('composer update');
    Artisan::call('key:generate');
    Artisan::call('migrate:fresh');
    Artisan::call('db:seed');
    // Artisan::call('storage:link');
});
