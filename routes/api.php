<?php

use App\Http\Controllers\API\KeuanganController;
use App\Http\Controllers\API\ProyekController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/login', [UserController::class, 'login']);
Route::post('/register', [UserController::class, 'register']);
Route::get('/proyek', [ProyekController::class, 'all']);
Route::get('/pengeluaran_proyek', [ProyekController::class, 'totalPengeluaranProyek']);
Route::post('/add_progress', [ProyekController::class, 'addprogress']);
Route::get('/get_progress', [ProyekController::class, 'getProgress']);
Route::post('/add_pengeluaran', [KeuanganController::class, 'addPengeluaran']);
Route::post('/add_pemasukan', [KeuanganController::class, 'addPemasukan']);




Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
