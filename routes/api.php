<?php

use App\Http\Controllers\ApiController;
use App\Http\Controllers\StokOpnameController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('stok-opname/{stokOpname:id}/stok', [StokOpnameController::class, 'stok'])->name('stok-opname.stok');
Route::get('stok-opname/{stokOpname:id}/unstock', [StokOpnameController::class, 'unstock'])->name('stok-opname.unstock');
Route::get('/last-id', [ApiController::class, 'lastBarang']);
Route::get('/master', [ApiController::class, 'getMaster']);
Route::post('/cek-tag', [ApiController::class, 'cekTag']);
Route::post('/create', [ApiController::class, 'create']);
Route::post('/input-stok', [ApiController::class, 'stok']);
Route::post('/sale', [ApiController::class, 'sale']);
