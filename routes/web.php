<?php

use App\Http\Controllers\allLaundryController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/index', [allLaundryController::class, 'index']);
