<?php

use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\LaundryController;
use App\Http\Controllers\Api\LayananController;
use App\Http\Controllers\Api\OwnerController;
use App\Http\Controllers\Api\PersonController;
use App\Http\Controllers\Api\PesananController;
use App\Http\Controllers\Api\TagihanController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\HargaController;
use App\Http\Controllers\Api\FavoriteLaundryController;
use App\Http\Controllers\Api\PelangganController;
use App\Http\Controllers\Api\PengeluaranController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes (No Auth Required)
|--------------------------------------------------------------------------
*/
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::post('/admin/login', [AdminController::class, 'login']);

Route::post('/owner/register', [OwnerController::class, 'register']);
Route::post('/owner/login', [OwnerController::class, 'login']);

/*
| Protected Routes (Auth Required)
*/
Route::middleware('auth:sanctum')->group(function () {

    // Tagihan
    Route::get('/tagihan/siap-ditagih', [TagihanController::class, 'getPesananSiapDitagih']);
    Route::get('/tagihan/belum-bayar', [TagihanController::class, 'belumBayar']);

    // User
    Route::get('/user', [AuthController::class, 'getUser']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::put('/profile', [AuthController::class, 'updateProfile']);
    Route::put('/change-password', [AuthController::class, 'changePassword']);

    // Favorite Laundry
    Route::get('/favorites', [FavoriteLaundryController::class, 'index']);
    Route::post('/favorites', [FavoriteLaundryController::class, 'store']);
    Route::delete('/favorites/{id}', [FavoriteLaundryController::class, 'destroy']);

    // Admin
    Route::post('/admin/logout', [AdminController::class, 'logout']);
    Route::get('/admin/me', [AdminController::class, 'getAdmin']);
    Route::resource('/admin', AdminController::class);

    // Owner
    Route::post('/owner/logout', [OwnerController::class, 'logout']);
    Route::get('/owner/profile', [OwnerController::class, 'profile']);
    Route::put('/owner/profile', [OwnerController::class, 'updateProfile']);
    Route::get('/owner/pesanan', [OwnerController::class, 'getPesanan']);
    Route::get('/owner/dashboard-stats', [OwnerController::class, 'getDashboardStats']);
    Route::get('/owner/pendapatan', [OwnerController::class, 'getPendapatan']);
    Route::prefix('owner')->group(function () {
        Route::get('/', [OwnerController::class, 'index']);
        Route::post('/', [OwnerController::class, 'store']);
        Route::get('/{id}', [OwnerController::class, 'show']);
        Route::put('/{id}', [OwnerController::class, 'update']);
        Route::delete('/{id}', [OwnerController::class, 'destroy']);
    });

    // Pengeluaran
    Route::get('/pengeluaran', [PengeluaranController::class, 'index']);
    Route::post('/pengeluaran', [PengeluaranController::class, 'store']);
    Route::get('/pengeluaran/{id}', [PengeluaranController::class, 'show']);
    Route::put('/pengeluaran/{id}', [PengeluaranController::class, 'update']);
    Route::delete('/pengeluaran/{id}', [PengeluaranController::class, 'destroy']);
    Route::get('/pengeluaran-kategori', [PengeluaranController::class, 'getKategori']);
    Route::get('/laporan-keuangan', [PengeluaranController::class, 'getLaporanBulanan']);

    // Person
    Route::resource('/person', PersonController::class);

    // Users
    Route::prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index']);
        Route::post('/', [UserController::class, 'store']);
        Route::get('/{id}', [UserController::class, 'show']);
        Route::put('/{id}', [UserController::class, 'update']);
        Route::delete('/{id}', [UserController::class, 'destroy']);
    });

    // Laundry
    Route::prefix('laundry')->group(function () {
        Route::get('/', [LaundryController::class, 'index']);
        Route::post('/', [LaundryController::class, 'store']);
        Route::get('/{id}', [LaundryController::class, 'show']);
        Route::put('/{id}', [LaundryController::class, 'update']);
        Route::delete('/{id}', [LaundryController::class, 'destroy']);
    });

    // Pesanan
    Route::prefix('pesanan')->group(function () {
        Route::get('/', [PesananController::class, 'index']);
        Route::post('/', [PesananController::class, 'store']);
        Route::get('/layanan', [PesananController::class, 'getLayananByOwner']);
        Route::get('/{id}', [PesananController::class, 'show']);
        Route::put('/{id}', [PesananController::class, 'update']);
        Route::delete('/{id}', [PesananController::class, 'destroy']);
    });

    // Pelanggan
    Route::prefix('pelanggan')->group(function () {
        Route::get('/', [PelangganController::class, 'index']);
        Route::post('/', [PelangganController::class, 'store']);
        Route::get('/search', [PelangganController::class, 'search']);
        Route::get('/admin/{adminId}', [PelangganController::class, 'getByAdmin']);
        Route::get('/{id}', [PelangganController::class, 'show']);
        Route::put('/{id}', [PelangganController::class, 'update']);
        Route::delete('/{id}', [PelangganController::class, 'destroy']);
    });

    // Layanan
    Route::prefix('layanan')->group(function () {
        Route::get('/', [LayananController::class, 'index']);
        Route::post('/', [LayananController::class, 'store']);
        Route::get('/stats', [LayananController::class, 'getStats']);
        Route::get('/by-tipe', [LayananController::class, 'getByTipe']);
        Route::get('/admin/{adminId}', [LayananController::class, 'getByAdmin']);
        Route::get('/{id}', [LayananController::class, 'show']);
        Route::put('/{id}', [LayananController::class, 'update']);
        Route::delete('/{id}', [LayananController::class, 'destroy']);
    });

    // Tagihan
    Route::prefix('tagihan')->group(function () {
        Route::get('/', [TagihanController::class, 'index']);
        Route::post('/', [TagihanController::class, 'store']);
        Route::get('/{id}', [TagihanController::class, 'show']);
        Route::put('/{id}', [TagihanController::class, 'update']);
        Route::apiResource('tagihan', TagihanController::class);
    });

    // Detail Harga
    Route::prefix('detailHarga')->group(function () {
        Route::get('/', [HargaController::class, 'index']);
        Route::post('/', [HargaController::class, 'store']);
        Route::get('/{id}', [HargaController::class, 'show']);
        Route::put('/{id}', [HargaController::class, 'update']);
        Route::delete('/{id}', [HargaController::class, 'destroy']);
    });
});
