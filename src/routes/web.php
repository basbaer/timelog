<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AddWorkerController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\WorkersOverviewController;

Route::get('/', [LoginController::class, 'show']);

// ==== Admin Routes ==========
// ---- Projects --------------
Route::get('/projects', function () {
    return view('admin/projects-overview');
});

Route::get('/projects/add', function () {
    return view('admin/projects-add');
});

Route::get('/projects/{id}', function ($id) {
    return view('admin/projects-detail', ['projectId' => $id]);
});


// ---- Workers --------------
Route::get('/workers', [WorkersOverviewController::class, 'show']);

// Create new worker
Route::post('/workers/add/request', [AddWorkerController::class, 'createUser']);

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

Route::post('/login', [LoginController::class, 'login']);