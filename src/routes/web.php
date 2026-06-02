<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AddWorkerController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\WorkersOverviewController;
use App\Http\Controllers\Auth\ActivationController;
use App\Http\Controllers\Auth\DeleteWorkerController;
use Illuminate\Support\Facades\App;
use App\Http\Middleware\Admin;
use App\Http\Controllers\LogoutController;
use App\Http\Controllers\Logs\ForstwirtLogController;
use App\Http\Controllers\WorkerDetailController;
use App\Http\Controllers\WorkerCardController;
use App\Http\Controllers\Projects\AddProjectController;
use App\Http\Controllers\Projects\OverviewProjectController;
use App\Http\Middleware\Forstwirt;
use App\Http\Middleware\Harvester;
use App\Http\Controllers\Logs\HarvesterLogController;

Route::get('/play', function () {
    return session()->all();
});



//==== Locale Settings ================================================

Route::get('/language/{locale?}', function ($locale) {
    // check if the locale is valid and set it in the session
    // Note: Middleware will always set the locale based on the session
    if ($locale && in_array($locale, config('app.available_locales'))) {
        App::setLocale($locale);
        session(['locale' => $locale]);
    }

    return redirect()->back();
});



//==== Log in / Activation Routes ================================================

Route::post('/createAdmin/request', [AddWorkerController::class, 'createUser']);

// Show success page after creating new admin
Route::get('/createAdmin/success', function () {
    return view('admin/auth/workers-add-success');
});

Route::get('/', [LoginController::class, 'show'])->name('login');

Route::post('/login', [LoginController::class, 'login']);

// Activation route for users with an activation code
Route::get('/activate', [ActivationController::class, 'show']);

// Handle activation form submission
Route::post('/activate', [ActivationController::class, 'activate']);

//================================================================================



//==== Logout Route =============================================================
Route::get('/logout', LogoutController::class)->middleware('auth');



// ==== Admin Routes =============================================================

Route::middleware(Admin::class)->prefix('admin')->group(function () {

    // ---- Projects --------------
    Route::get('/projects', [OverviewProjectController::class, 'show'])->name('admin.projects.overview');

    Route::get('/projects/add', [AddProjectController::class, 'show'])->name('admin.projects.add');

    Route::get('/projects/{id}', function ($id) {
        return view('admin/projects-detail', ['projectId' => $id]);
    })->name('admin.projects.detail');

    Route::post('/projects/add/request', [AddProjectController::class, 'store'])->name('admin.projects.store');


    // ---- Workers --------------
    Route::get('/workers', [WorkersOverviewController::class, 'show'])->name('admin.workers.overview');

    // Create new worker
    // Show form to create new worker
    Route::get('/workers/add', [AddWorkerController::class, 'show'])->name('admin.workers.add');

    // Handle form submission to create new worker
    Route::post('/workers/add/request', [AddWorkerController::class, 'createUser']);

    // Show success page after creating new worker
    Route::get('/workers/add/success', function () {
        return view('admin/auth/workers-add-success');
    });

    // Show worker details
    Route::get('/workers/{id}', [WorkerDetailController::class, 'show'])->name('admin.worker.show');

    // Show worker card
    Route::get('/workers/{id}/card', [WorkerCardController::class, 'show'])->name('admin.worker.card');

    // Deletet Worker
    Route::post('/workers/{id}/delete', [DeleteWorkerController::class, 'deleteWorker'])->name('admin.worker.delete');

    Route::get('/workers/{id}/log/create', [ForstwirtLogController::class, 'show'])->name('admin.worker.log.create');
});


//====================================================================================

// ==== Forstwirt Route ====
Route::get('/log-forstwirt', [ForstwirtLogController::class, 'show'])
    ->name('log.forstwirt')
    ->middleware([Forstwirt::class]);

Route::post('/log-forstwirt', [ForstwirtLogController::class, 'store'])
    ->name('log.forstwirt.store')
    ->middleware([Forstwirt::class]);

Route::get('/log-forstwirt/success', [ForstwirtLogController::class, 'success'])
    ->name('log.forstwirt.success')
    ->middleware([Forstwirt::class]);

Route::delete('/log-forstwirt/{log_id}/delete', [ForstwirtLogController::class, 'deleteLog'])
    ->middleware([Forstwirt::class])
    ->name('log.forstwirt.delete');

//====================================================================================

// ==== Harvester Route ====
Route::middleware(Harvester::class)->group(function () {
    Route::get('/log-harvester', [HarvesterLogController::class, 'show'])->name('log.harvester');

    Route::post('/log-harvester', [HarvesterLogController::class, 'store'])->name('log.harvester.store');

    Route::get('/log-harvester/success/', [HarvesterLogController::class, 'success'])->name('log.harvester.success');

    Route::delete('/log-harvester/{log_id}/delete', [HarvesterLogController::class, 'deleteLog'])->name('log.harvester.delete');
});


// ==== Later ====
Route::get('/workers/{id}', function ($id) {
    return view('admin/workers-detail', ['workerId' => $id]);
});

Route::get('/workers/{id}/cards', function ($id) {
    return view('admin/workers-card', ['workerId' => $id]);
});
