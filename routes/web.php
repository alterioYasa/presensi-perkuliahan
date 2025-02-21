<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PresensiController;
use App\Http\Controllers\RekapPresensiController;
use App\Http\Controllers\RevisiPresensiController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth.dosen'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/presensi/{kode_mk}/{semester}', [PresensiController::class, 'inputPresensi'])->name('input-presensi');
    Route::post('/presensi/simpan', [PresensiController::class, 'simpanPresensi'])->name('simpan-presensi');

    Route::get('/revisi-presensi/{kode_mk}/{semester}', [RevisiPresensiController::class, 'revisiPresensi'])->name('input-revisi-presensi');
    Route::post('/revisi-presensi/simpan', [RevisiPresensiController::class, 'simpanRevisiPresensi'])->name('simpan-revisi-presensi');

    Route::get('/rekap-presensi/{kode_mk}/{semester}', [RekapPresensiController::class, 'generatePDF'])->name('generate-pdf-rekap');
});
