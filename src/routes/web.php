<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::get('/', function () {
    return view('logIn');
});

Route::post('/dashboard', function () {
    return view('dashboard');
});

Route::post('/timelog', [UserController::class, 'showTimelog']);