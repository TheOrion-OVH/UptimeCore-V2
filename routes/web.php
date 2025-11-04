<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\MonitorController;
use App\Http\Controllers\Web\IncidentController;
use App\Http\Controllers\Web\MaintenanceController;
use App\Http\Controllers\Web\StatusPageController;

Route::get('/', function () {
    return redirect('/dashboard');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('monitors', MonitorController::class);
    Route::resource('incidents', IncidentController::class);
    Route::resource('maintenances', MaintenanceController::class);
    Route::get('/status-page', [StatusPageController::class, 'index'])->name('status-page.index');
});

require __DIR__.'/auth.php';
