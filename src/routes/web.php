<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::get('/', function () {
    return view('logIn');
});

# ==== Supervisor Routes ==========
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

Route::post('/workers/add/request', [UserController::class, 'register']);

Route::get('/workers/add', function () {
    return view('admin/workers-add');
});

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