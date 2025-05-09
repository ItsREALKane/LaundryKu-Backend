<?php

use App\Http\Controllers\allLaundryController;
use App\Http\Controllers\Api\AuthController;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/index', [allLaundryController::class, 'index']);

