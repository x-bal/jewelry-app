<?php

use App\Http\Controllers\BarangController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LocatorController;
use App\Http\Controllers\SatuanController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\TipeBarangController;
use App\Http\Controllers\TransactionController;
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
    return view('welcome');
});

Auth::routes();

Route::middleware('auth')->group(function () {
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
});
