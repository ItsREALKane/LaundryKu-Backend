<?php

use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\LaundryController;
use App\Http\Controllers\Api\PersonController;
use App\Http\Controllers\Api\PesananController;
use App\Http\Controllers\Api\TagihanController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::resource('/person', PersonController::class);
Route::post('/person', [PersonController::class, 'store']);

Route::resource('/admin', AdminController::class);
Route::post('/admin', [AdminController::class, 'store']);


Route::prefix('users')->group(function () {
    Route::get('/', [UserController::class, 'index']);
    Route::post('/', [UserController::class, 'store']);
    Route::get('/{id}', [UserController::class, 'show']);
    Route::put('/{id}', [UserController::class, 'update']);
    Route::delete('/{id}', [UserController::class, 'destroy']);
});

Route::prefix('laundry')->group(function () {
    Route::get('/', [LaundryController::class, 'index']);
    Route::post('/', [LaundryController::class, 'store']);
    Route::get('/{id}', [LaundryController::class, 'show']);
    Route::put('/{id}', [LaundryController::class, 'update']);
    Route::delete('/{id}', [LaundryController::class, 'destroy']);
});

Route::prefix('pesanan')->group(function () {
    Route::get('/', [PesananController::class, 'index']);
    Route::post('/', [PesananController::class, 'store']);
    Route::get('/{id}', [PesananController::class, 'show']);
    Route::put('/{id}', [PesananController::class, 'update']);
    Route::delete('/{id}', [PesananController::class, 'destroy']);
});

Route::prefix('tagihan')->group(function () {
    Route::get('/', [TagihanController::class, 'index']);
    Route::post('/', [TagihanController::class, 'store']);
    Route::get('/{id}', [TagihanController::class, 'show']);
    Route::put('/{id}', [TagihanController::class, 'update']);
    Route::delete('/{id}', [TagihanController::class, 'destroy']);
});

