<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AddWorkerController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\WorkersOverviewController;
use App\Http\Controllers\Auth\ActivationController;

//==== Log in / Activation Routes ================================================
Route::get('/', [LoginController::class, 'show']);

Route::post('/login', [LoginController::class, 'login']);

// Activation route for users with an activation code
Route::get('/activate', [ActivationController::class, 'show']);

// ==== Admin Routes =============================================================
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
// Show form to create new worker
Route::get('/workers/add', [AddWorkerController::class, 'show']);

// Handle form submission to create new worker
Route::post('/workers/add/request', [AddWorkerController::class, 'createUser']);

// Show success page after creating new worker
Route::get('/workers/add/success', function () {
    return view('admin/auth/workers-add-success');
});

//====================================================================================



// ==== Later ====
Route::get('/workers/{id}', function ($id) {
    return view('admin/workers-detail', ['workerId' => $id]);
});

Route::get('/workers/{id}/cards', function ($id) {
    return view('admin/workers-card', ['workerId' => $id]);
});

