<?php

use App\Http\Controllers\Auth\ActivationController;
use App\Http\Controllers\Auth\AddWorkerController;
use App\Http\Controllers\Auth\DeleteWorkerController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\LogoutController;
use App\Http\Controllers\Logs\ForstwirtLogController;
use App\Http\Controllers\Logs\HarvesterLogController;
use App\Http\Controllers\Logs\RueckezugLogController;
use App\Http\Controllers\PrintController;
use App\Http\Controllers\Projects\AddProjectController;
use App\Http\Controllers\Projects\OverviewProjectController;
use App\Http\Controllers\Projects\ProjectDetailController;
use App\Http\Controllers\WorkerCardController;
use App\Http\Controllers\WorkerDetailController;
use App\Http\Controllers\WorkerSettingsController;
use App\Http\Controllers\WorkersOverviewController;
use App\Http\Middleware\Admin;
use App\Http\Middleware\Harvester;
use App\Http\Middleware\Rueckezug;
use App\Http\Middleware\IsLoggedIn;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;

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
// If no user exists in the database, a form is shown to register the admin
Route::post('/createAdmin/request', [AddWorkerController::class, 'createUser']);

// Show success page after creating new admin
Route::get('/createAdmin/success', function () {
    return view('admin/auth/workers-add-success');
});

Route::get('/', [LoginController::class, 'show'])->name('login');

Route::post('/login', [LoginController::class, 'login']);

// Activation route for users with an activation code
Route::get('/activate', [ActivationController::class, 'show'])->name('activate');

// Handle activation form submission
Route::post('/activate', [ActivationController::class, 'activate']);

Route::get('/password-reset', [ActivationController::class, 'showPasswordResetForm'])->name('password-reset');

Route::post('/password-reset', [ActivationController::class, 'resetPassword'])->name('password-reset');
//================================================================================



//==== Logout Route ==============================================================
Route::get('/logout', LogoutController::class)->middleware('auth')->name('logout');

Route::get('/impressum', function () {
    return view('impressum');
})->name('impressum');

// ==== Admin Routes =============================================================

Route::middleware(Admin::class)->prefix('admin')->group(function () {

    // ---- Projects --------------
    Route::get('/projects', [OverviewProjectController::class, 'show'])->name('admin.projects.overview');

    Route::get('/projects/add', [AddProjectController::class, 'show'])->name('admin.projects.add');

    Route::get('/projects/closed', [OverviewProjectController::class, 'showClosed'])->name('admin.projects.closed');

    Route::get('/projects/{id}', [ProjectDetailController::class, 'show'])
        ->whereNumber('id')
        ->name('admin.project.detail');

    Route::post('/projects/add/request', [AddProjectController::class, 'store'])->name('admin.projects.store');

    Route::get('/projects/{id}/edit', [AddProjectController::class, 'edit'])->name('admin.projects.edit');

    Route::put('/projects/{id}/update', [AddProjectController::class, 'update'])->name('admin.projects.update');

    Route::put('/projects/{id}/close', [ProjectDetailController::class, 'close'])->name('admin.projects.close');

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

    // Delete log entry for worker
    Route::delete('/workers/{worker_id}/log/{log_id}/delete', [WorkerDetailController::class, 'deleteLog'])->name('admin.worker.log.delete');

    // Open the corresponding log form in edit mode
    Route::get('/workers/{worker_id}/log/{log_id}/edit', [WorkerDetailController::class, 'editLog'])->name('admin.worker.log.edit');

    // Show worker details for printing
    Route::get('/workers/{worker_id}/print/{project}', [WorkerDetailController::class, 'print'])->name('workers.print');

    // Show worker card
    Route::get('/workers/{worker_id}/card', [WorkerCardController::class, 'show'])->name('admin.worker.card');

    // Deletet Worker
    Route::post('/workers/{worker_id}/delete', [DeleteWorkerController::class, 'deleteWorker'])->name('admin.worker.delete');

    Route::get('/workers/{worker_id}/log/create', [WorkerDetailController::class, 'addWorkLog'])->name('admin.worker.log.create');
});

//====================================================================================

// ==== Worker and Admin Routes ======================================================
Route::middleware([IsLoggedIn::class])->group(function () {
    Route::get('/workers/{worker_id}/preparePrint', [PrintController::class, 'preparePrint'])
        ->name('workers.preparePrint');

    Route::post('/workers/{worker_id}/preparePrint', [PrintController::class, 'loadPrint'])
        ->name('workers.preparePrint.post');

    Route::get('/workers/{worker_id}/preparePrint/loadClosedProjects', [PrintController::class, 'loadClosedProjects'])
        ->name('workers.preparePrint.loadClosedProjects');

    Route::get('/workers/{worker_id}/print/', [PrintController::class, 'print'])
        ->name('print.show');

    // Show working hours overview for a specific worker
    Route::get('/workers/{worker_id}', [WorkerDetailController::class, 'show'])->name('worker.show');

    Route::get('/workers/{worker_id}/settings', [WorkerSettingsController::class, 'show'])->name('worker.settings');

    Route::post('/workers/{worker_id}/password-change', [WorkerSettingsController::class, 'changePassword'])->name('worker.password.change');

    // ==== Forstwirt Route ====
    Route::get('/log-forstwirt/{user_id?}', [ForstwirtLogController::class, 'show'])
        ->name('log.forstwirt');

    Route::get('/log-forstwirt/{user_id}/edit/{log_id}', [ForstwirtLogController::class, 'edit'])
        ->name('log.forstwirt.edit');

    Route::post('/log-forstwirt', [ForstwirtLogController::class, 'store'])
        ->name('log.forstwirt.store');

    Route::put('/log-forstwirt/{user_id}/edit/{log_id}', [ForstwirtLogController::class, 'update'])
        ->name('log.forstwirt.update');

    Route::get('/log-forstwirt/{worker_id}/success', [ForstwirtLogController::class, 'success'])
        ->name('log.forstwirt.success');

    Route::delete('/log-forstwirt/{worker_id}/delete', [ForstwirtLogController::class, 'deleteLog'])
        ->name('log.forstwirt.delete');
});

//====================================================================================

// ==== Harvester Route ====
Route::middleware(Harvester::class)->group(function () {
    Route::get('/log-harvester/{user_id?}', [HarvesterLogController::class, 'show'])->name('log.harvester');

    Route::get('/log-harvester/{user_id}/edit/{log_id}', [HarvesterLogController::class, 'edit'])->name('log.harvester.edit');

    Route::post('/log-harvester', [HarvesterLogController::class, 'store'])->name('log.harvester.store');

    Route::put('/log-harvester/{user_id}/edit/{log_id}', [HarvesterLogController::class, 'update'])->name('log.harvester.update');

    Route::get('/log-harvester/{worker_id}/success', [HarvesterLogController::class, 'success'])->name('log.harvester.success');

    Route::delete('/log-harvester/{worker_id}/delete', [HarvesterLogController::class, 'deleteLog'])->name('log.harvester.delete');
});

//====================================================================================

// ==== Rueckezug Route ====
Route::middleware(Rueckezug::class)->group(function () {
    Route::get('/log-rueckezug/{user_id?}', [RueckezugLogController::class, 'show'])->name('log.rueckezug');

    Route::get('/log-rueckezug/{user_id}/edit/{log_id}', [RueckezugLogController::class, 'edit'])->name('log.rueckezug.edit');

    Route::post('/log-rueckezug', [RueckezugLogController::class, 'store'])->name('log.rueckezug.store');

    Route::put('/log-rueckezug/{user_id}/edit/{log_id}', [RueckezugLogController::class, 'update'])->name('log.rueckezug.update');

    Route::get('/log-rueckezug/{worker_id}/success', [RueckezugLogController::class, 'success'])->name('log.rueckezug.success');

    Route::delete('/log-rueckezug/{worker_id}/delete', [RueckezugLogController::class, 'deleteLog'])->name('log.rueckezug.delete');
});
