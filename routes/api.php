<?php

use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\LaundryController;
use App\Http\Controllers\Api\OwnerController;
use App\Http\Controllers\Api\PersonController;
use App\Http\Controllers\Api\PesananController;
use App\Http\Controllers\Api\TagihanController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\HargaController;
use App\Http\Controllers\Api\FavoriteLaundryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// User Authentication Routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// Admin Authentication Routes
Route::post('/admin/login', [AdminController::class, 'login']);

// Owner Authentication Routes
Route::post('/owner/register', [OwnerController::class, 'register']);
Route::post('/owner/login', [OwnerController::class, 'login']);

// Protected User Routes (requires auth:sanctum)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'getUser']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::put('/profile', [AuthController::class, 'updateProfile']);
    Route::put('/change-password', [AuthController::class, 'changePassword']);
    
    // Protected Favorite Laundry Routes
    Route::get('/favorites', [FavoriteLaundryController::class, 'index']);
    Route::post('/favorites', [FavoriteLaundryController::class, 'store']);
    Route::delete('/favorites/{id}', [FavoriteLaundryController::class, 'destroy']);
});

// Protected Admin Routes (requires auth:sanctum)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/admin/logout', [AdminController::class, 'logout']);
    Route::get('/admin/me', [AdminController::class, 'getAdmin']);
});

// Protected Owner Routes (requires auth:sanctum)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/owner/logout', [OwnerController::class, 'logout']);
    Route::get('/owner/profile', [OwnerController::class, 'profile']);
    Route::put('/owner/profile', [OwnerController::class, 'updateProfile']);
    Route::get('/owner/pesanan', [OwnerController::class, 'getPesanan']);
    Route::get('/owner/dashboard-stats', [OwnerController::class, 'getDashboardStats']);
});

// Person Routes
Route::resource('/person', PersonController::class);

// Admin CRUD Routes
Route::resource('/admin', AdminController::class);

// User CRUD Routes
Route::prefix('users')->group(function () {
    Route::get('/', [UserController::class, 'index']);
    Route::post('/', [UserController::class, 'store']);
    Route::get('/{id}', [UserController::class, 'show']);
    Route::put('/{id}', [UserController::class, 'update']);
    Route::delete('/{id}', [UserController::class, 'destroy']);
});

// Laundry Routes
Route::prefix('laundry')->group(function () {
    Route::get('/', [LaundryController::class, 'index']);
    Route::post('/', [LaundryController::class, 'store']);
    Route::get('/{id}', [LaundryController::class, 'show']);
    Route::put('/{id}', [LaundryController::class, 'update']);
    Route::delete('/{id}', [LaundryController::class, 'destroy']);
});

// Pesanan Routes
Route::prefix('pesanan')->group(function () {
    Route::get('/', [PesananController::class, 'index']);
    Route::post('/', [PesananController::class, 'store']);
    Route::get('/{id}', [PesananController::class, 'show']);
    Route::put('/{id}', [PesananController::class, 'update']);
    Route::delete('/{id}', [PesananController::class, 'destroy']);
});

// Tagihan Routes
Route::prefix('tagihan')->group(function () {
    Route::get('/', [TagihanController::class, 'index']);
    Route::post('/', [TagihanController::class, 'store']);
    Route::get('/{id}', [TagihanController::class, 'show']);
    Route::put('/{id}', [TagihanController::class, 'update']);
    Route::delete('/{id}', [TagihanController::class, 'destroy']);
});

// Detail Harga Routes
Route::prefix('detailHarga')->group(function () {
    Route::get('/', [HargaController::class, 'index']);
    Route::post('/', [HargaController::class, 'store']);
    Route::get('/{id}', [HargaController::class, 'show']);
    Route::put('/{id}', [HargaController::class, 'update']);
    Route::delete('/{id}', [HargaController::class, 'destroy']);
});

// Owner CRUD Routes
Route::prefix('owner')->group(function () {
    Route::get('/', [OwnerController::class, 'index']);
    Route::post('/', [OwnerController::class, 'store']);
    Route::get('/{id}', [OwnerController::class, 'show']);
    Route::put('/{id}', [OwnerController::class, 'update']);
    Route::delete('/{id}', [OwnerController::class, 'destroy']);
});