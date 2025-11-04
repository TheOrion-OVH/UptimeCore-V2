<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MonitorController;
use App\Http\Controllers\Api\IncidentController;
use App\Http\Controllers\Api\MaintenanceController;
use App\Http\Controllers\Api\NotificationChannelController;
use App\Http\Controllers\Api\StatusPageController;
use App\Http\Controllers\Api\DashboardController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('api')->group(function () {
    
    // Public routes
    Route::get('/status/{slug}', [StatusPageController::class, 'public'])->name('api.status.public');
    
    // Auth routes
    Route::prefix('auth')->group(function () {
        Route::post('/login', [AuthController::class, 'login'])->name('api.auth.login');
        Route::middleware('auth:sanctum')->group(function () {
            Route::post('/logout', [AuthController::class, 'logout'])->name('api.auth.logout');
            Route::post('/refresh', [AuthController::class, 'refresh'])->name('api.auth.refresh');
        });
    });
    
    // Protected routes
    Route::middleware(['auth:sanctum', 'throttle:api'])->group(function () {
        
        // Monitors
        Route::prefix('monitors')->group(function () {
            Route::get('/', [MonitorController::class, 'index'])->name('api.monitors.index');
            Route::post('/create', [MonitorController::class, 'store'])->name('api.monitors.store');
            Route::get('/{id}', [MonitorController::class, 'show'])->name('api.monitors.show');
            Route::put('/{id}/update', [MonitorController::class, 'update'])->name('api.monitors.update');
            Route::delete('/{id}/delete', [MonitorController::class, 'destroy'])->name('api.monitors.destroy');
            Route::post('/{id}/pause', [MonitorController::class, 'pause'])->name('api.monitors.pause');
            Route::post('/{id}/resume', [MonitorController::class, 'resume'])->name('api.monitors.resume');
            Route::post('/{id}/check', [MonitorController::class, 'check'])->name('api.monitors.check');
            Route::get('/{id}/stats', [MonitorController::class, 'stats'])->name('api.monitors.stats');
            Route::get('/{id}/heartbeats', [MonitorController::class, 'heartbeats'])->name('api.monitors.heartbeats');
            Route::get('/{id}/uptime-daily', [MonitorController::class, 'uptimeDaily'])->name('api.monitors.uptime-daily');
        });
        
        // Incidents
        Route::prefix('incidents')->group(function () {
            Route::get('/', [IncidentController::class, 'index'])->name('api.incidents.index');
            Route::post('/create', [IncidentController::class, 'store'])->name('api.incidents.store');
            Route::get('/{id}', [IncidentController::class, 'show'])->name('api.incidents.show');
            Route::put('/{id}/update', [IncidentController::class, 'update'])->name('api.incidents.update');
            Route::post('/{id}/resolve', [IncidentController::class, 'resolve'])->name('api.incidents.resolve');
            Route::delete('/{id}/delete', [IncidentController::class, 'destroy'])->name('api.incidents.destroy');
        });
        
        // Maintenances
        Route::prefix('maintenances')->group(function () {
            Route::get('/', [MaintenanceController::class, 'index'])->name('api.maintenances.index');
            Route::post('/create', [MaintenanceController::class, 'store'])->name('api.maintenances.store');
            Route::get('/{id}', [MaintenanceController::class, 'show'])->name('api.maintenances.show');
            Route::put('/{id}/update', [MaintenanceController::class, 'update'])->name('api.maintenances.update');
            Route::post('/{id}/start', [MaintenanceController::class, 'start'])->name('api.maintenances.start');
            Route::post('/{id}/complete', [MaintenanceController::class, 'complete'])->name('api.maintenances.complete');
            Route::delete('/{id}/delete', [MaintenanceController::class, 'destroy'])->name('api.maintenances.destroy');
        });
        
        // Notification Channels
        Route::prefix('notification-channels')->group(function () {
            Route::get('/', [NotificationChannelController::class, 'index'])->name('api.notification-channels.index');
            Route::post('/create', [NotificationChannelController::class, 'store'])->name('api.notification-channels.store');
            Route::get('/{id}', [NotificationChannelController::class, 'show'])->name('api.notification-channels.show');
            Route::put('/{id}/update', [NotificationChannelController::class, 'update'])->name('api.notification-channels.update');
            Route::post('/{id}/test', [NotificationChannelController::class, 'test'])->name('api.notification-channels.test');
            Route::delete('/{id}/delete', [NotificationChannelController::class, 'destroy'])->name('api.notification-channels.destroy');
        });
        
        // Status Page
        Route::prefix('status-page')->group(function () {
            Route::get('/', [StatusPageController::class, 'show'])->name('api.status-page.show');
            Route::put('/update', [StatusPageController::class, 'update'])->name('api.status-page.update');
        });
        
        // Dashboard
        Route::prefix('dashboard')->group(function () {
            Route::get('/stats', [DashboardController::class, 'stats'])->name('api.dashboard.stats');
        });
    });
});

