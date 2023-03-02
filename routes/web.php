<?php

use App\Http\Controllers\BarangController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\LocatorController;
use App\Http\Controllers\LostStokController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\SatuanController;
use App\Http\Controllers\StokOpnameController;
use App\Http\Controllers\TipeBarangController;
use App\Http\Controllers\UserController;
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

    Route::get('users/get', [UserController::class, 'get'])->name('users.list');
    Route::resource('users', UserController::class);

    Route::get('locators/get', [LocatorController::class, 'get'])->name('locators.list');
    Route::resource('locators', LocatorController::class);

    Route::get('satuan/get', [SatuanController::class, 'get'])->name('satuan.list');
    Route::resource('satuan', SatuanController::class);

    Route::get('tipe-barang/get', [TipeBarangController::class, 'get'])->name('tipe-barang.list');
    Route::resource('tipe-barang', TipeBarangController::class);

    Route::get('barang/get', [BarangController::class, 'get'])->name('barang.list');
    Route::resource('barang', BarangController::class);

    Route::get('stok-opname/get', [StokOpnameController::class, 'get'])->name('stok-opname.list');
    Route::get('stok-opname/{stokOpname:id}/find', [StokOpnameController::class, 'find'])->name('stok-opname.find');
    Route::get('stok-opname/change', [StokOpnameController::class, 'change'])->name('stok-opname.change');
    Route::get('stok-opname/{stokOpname:id}/save', [StokOpnameController::class, 'save'])->name('stok-opname.save');
    Route::resource('stok-opname', StokOpnameController::class);

    Route::get('lost-stok/get', [LostStokController::class, 'get'])->name('lost-stok.list');
    Route::get('lost-stok/{lostStok:id}/lost', [LostStokController::class, 'lost'])->name('lost-stok.lost');
    Route::resource('lost-stok', LostStokController::class);

    Route::get('penjualan/get', [PenjualanController::class, 'get'])->name('penjualan.list');
    Route::get('penjualan/get-list/{penjualan:id}', [PenjualanController::class, 'getList'])->name('penjualan.get');
    Route::resource('penjualan', PenjualanController::class);

    Route::get('devices/get', [DeviceController::class, 'get'])->name('devices.list');
    Route::post('devices/{device:id}/pairing', [DeviceController::class, 'pairing'])->name('devices.pairing');
    Route::resource('devices', DeviceController::class);
});
