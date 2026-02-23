<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AddWorkerController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\WorkersOverviewController;
use App\Http\Controllers\Auth\ActivationController;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Session\Session;

Route::get('/play', function () {
    return session()->all();
});
//==== Locale Settings ================================================
Route::get('/language/{locale?}', function ($locale) {
    // check if the locale is valid and set it in the session
    // Note: Middleware will always set the locale based on the session
    if ($locale && in_array($locale, ['en', 'de', 'ro'])) {
        App::setLocale($locale);
        session(['locale' => $locale]);
    }

    return App::getLocale();
});

//==== Log in / Activation Routes ================================================
Route::get('/', [LoginController::class, 'show']);

Route::post('/login', [LoginController::class, 'login']);

// Activation route for users with an activation code
Route::get('/activate', [ActivationController::class, 'show']);

// Handle activation form submission
Route::post('/activate', [ActivationController::class, 'activate']);

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

