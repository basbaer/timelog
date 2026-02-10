<?php

use App\Http\Controllers\RoleController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AddWorkerController;

Route::get('/', function () {
    return view('logIn');
});

# ==== Admin Routes ==========
# ---- Projects --------------
Route::get('/projects', function () {
    return view('admin/projects-overview');
});

Route::get('/projects/add', function () {
    return view('admin/projects-add');
});

Route::get('/projects/{id}', function ($id) {
    return view('admin/projects-detail', ['projectId' => $id]);
});


# ---- Workers --------------
Route::get('/workers', function () {
    return view('admin/workers-overview');
});

Route::post('/workers/add/request', [UserController::class, 'create']);

Route::get('/workers/add', [AddWorkerController::class, 'show']);

Route::get('/workers/add/success', function () {
    return view('admin/workers-add-success');
});

Route::get('/workers/{id}', function ($id) {
    return view('admin/workers-detail', ['workerId' => $id]);
});

Route::get('/workers/{id}/cards', function ($id) {
    return view('admin/workers-card', ['workerId' => $id]);
});

Route::post('/timelog', [UserController::class, 'generateUsername']);