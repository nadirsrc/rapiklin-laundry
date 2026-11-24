<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CekCucianController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\LayananController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return Auth::check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

Route::get('/cek-cucian', [CekCucianController::class, 'index'])->name('cek-cucian');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
});

Route::middleware(['auth', 'role:admin,kasir'])->group(function (): void {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Data Layanan: daftar dapat dilihat Admin & Kasir, sedangkan CRUD hanya untuk Admin.
    Route::get('/layanan', [LayananController::class, 'index'])->name('layanan.index');
    Route::middleware('role:admin')->group(function (): void {
        Route::get('/layanan/create', [LayananController::class, 'create'])->name('layanan.create');
        Route::post('/layanan', [LayananController::class, 'store'])->name('layanan.store');
        Route::get('/layanan/{layanan}/edit', [LayananController::class, 'edit'])->name('layanan.edit');
        Route::put('/layanan/{layanan}', [LayananController::class, 'update'])->name('layanan.update');
        Route::delete('/layanan/{layanan}', [LayananController::class, 'destroy'])->name('layanan.destroy');
    });

    // Transaksi Laundry: operasional harian Admin & Kasir.
    Route::get('/transaksi', [TransaksiController::class, 'index'])->name('transaksi.index');
    Route::get('/transaksi/create', [TransaksiController::class, 'create'])->name('transaksi.create');
    Route::post('/transaksi', [TransaksiController::class, 'store'])->name('transaksi.store');
    Route::get('/transaksi/{transaksi}', [TransaksiController::class, 'show'])->name('transaksi.show');
    Route::get('/transaksi/{transaksi}/nota', [TransaksiController::class, 'nota'])->name('transaksi.nota');
    Route::patch('/transaksi/{transaksi}/status', [TransaksiController::class, 'updateStatus'])->name('transaksi.status');
    Route::patch('/transaksi/{transaksi}/pembayaran', [TransaksiController::class, 'updatePembayaran'])->name('transaksi.pembayaran');
    Route::delete('/transaksi/{transaksi}', [TransaksiController::class, 'destroy'])
        ->middleware('role:admin')
        ->name('transaksi.destroy');

    // Pelanggan & Rekap Transaksi.
    Route::get('/pelanggan', [PelangganController::class, 'index'])->name('pelanggan.index');
    Route::get('/pelanggan/{pelanggan}', [PelangganController::class, 'show'])->name('pelanggan.show');

    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/export', [LaporanController::class, 'exportCsv'])->name('laporan.export');
    Route::get('/laporan/export-excel', [LaporanController::class, 'exportXlsx'])->name('laporan.export.xlsx');

    // Manajemen Pengguna khusus Admin.
    Route::middleware('role:admin')->group(function (): void {
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::patch('/users/{user}/toggle', [UserController::class, 'toggleActive'])->name('users.toggle');
    });
});
