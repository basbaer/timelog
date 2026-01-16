<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::get('/', function () {
    return view('logIn');
});

# ==== Supervisor Routes ==========
# ---- Projects --------------
Route::get('/projects', function () {
    return view('supervisor/projects-overview');
});

Route::get('/projects/add', function () {
    return view('supervisor/projects-add');
});

Route::get('/projects/{id}', function ($id) {
    return view('supervisor/projects-detail', ['projectId' => $id]);
});


# ---- Workers --------------
Route::get('/workers', function () {
    return view('supervisor/workers-overview');
});

Route::post('/workers/add/request', [UserController::class, 'generateUsername']);

Route::get('/workers/add', function () {
    return view('supervisor/workers-add');
});

Route::get('/workers/add/success', function () {
    return view('supervisor/workers-add-success');
});

Route::get('/workers/{id}', function ($id) {
    return view('supervisor/workers-detail', ['workerId' => $id]);
});

Route::get('/workers/{id}/cards', function ($id) {
    return view('supervisor/workers-card', ['workerId' => $id]);
});

Route::post('/timelog', [UserController::class, 'generateUsername']);