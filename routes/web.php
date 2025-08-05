<?php

use App\Http\Controllers\allLaundryController;
use App\Http\Controllers\Api\AuthController;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test-xendit', function () {
    return view('test-xendit-api');
}); 

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('admin.login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/index', [allLaundryController::class, 'index']);
    Route::get('/dashboard', [allLaundryController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/orders', [allLaundryController::class, 'orders'])->name('admin.orders');
    Route::get('/bills', [allLaundryController::class, 'bills'])->name('admin.bills');
    Route::get('/history', [allLaundryController::class, 'history'])->name('admin.history');
    Route::get('/settings', [allLaundryController::class, 'settings'])->name('admin.settings');
});

