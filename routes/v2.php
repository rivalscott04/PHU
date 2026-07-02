<?php

use App\Http\Controllers\V2\AuditLogController;
use App\Http\Controllers\V2\ChecklistController;
use App\Http\Controllers\V2\ComplianceProfileController;
use App\Http\Controllers\V2\ExecutiveDashboardController;
use App\Http\Controllers\V2\ExportController;
use App\Http\Controllers\V2\FollowupController;
use App\Http\Controllers\V2\InspectionController;
use App\Http\Controllers\V2\MonitoringController;
use App\Http\Controllers\V2\NotificationController;
use App\Http\Controllers\V2\RiskController;
use App\Models\Inspection;
use Illuminate\Support\Facades\Route;

Route::bind('pengawasan', fn (string $value) => Inspection::findOrFail($value));

Route::middleware(['auth', 'password.changed'])->prefix('v2')->name('v2.')->group(function () {
    Route::get('/dashboard/statistics', [ExecutiveDashboardController::class, 'statistics'])->name('dashboard.statistics');
    Route::get('/dashboard/charts', [ExecutiveDashboardController::class, 'charts'])->name('dashboard.charts');
    Route::get('/dashboard/ranking', [ExecutiveDashboardController::class, 'ranking'])->name('dashboard.ranking');
    Route::get('/dashboard/timeline', [ExecutiveDashboardController::class, 'timeline'])->name('dashboard.timeline');
    Route::get('/dashboard/warning', [ExecutiveDashboardController::class, 'warning'])->name('dashboard.warning');
    Route::get('/dashboard/heatmap', [ExecutiveDashboardController::class, 'heatmap'])->name('dashboard.heatmap');
    Route::get('/dashboard', [ExecutiveDashboardController::class, 'index'])->name('dashboard');

    Route::prefix('monitoring')->name('monitoring.')->group(function () {
        Route::get('/', [MonitoringController::class, 'index'])->name('index');
        Route::get('/statistics', [MonitoringController::class, 'statistics'])->name('statistics');
        Route::get('/travel', [MonitoringController::class, 'travel'])->name('travel');
    });

    Route::prefix('pengawasan')->name('pengawasan.')->group(function () {
        Route::get('/', [InspectionController::class, 'index'])->name('index');
        Route::get('/create', [InspectionController::class, 'create'])->name('create');
        Route::post('/', [InspectionController::class, 'store'])->name('store');
        Route::get('/{pengawasan}', [InspectionController::class, 'show'])->name('show');
        Route::get('/{pengawasan}/edit', [InspectionController::class, 'edit'])->name('edit');
        Route::put('/{pengawasan}', [InspectionController::class, 'update'])->name('update');
        Route::post('/{pengawasan}/temuan', [InspectionController::class, 'storeFinding'])->name('temuan.store');
        Route::put('/{pengawasan}/checklist', [InspectionController::class, 'updateChecklists'])->name('checklist.update');
    });

    Route::prefix('master/checklist')->name('checklist.')->group(function () {
        Route::get('/', [ChecklistController::class, 'index'])->name('index');
        Route::get('/create', [ChecklistController::class, 'create'])->name('create');
        Route::post('/', [ChecklistController::class, 'store'])->name('store');
        Route::get('/{checklist}/edit', [ChecklistController::class, 'edit'])->name('edit');
        Route::put('/{checklist}', [ChecklistController::class, 'update'])->name('update');
        Route::delete('/{checklist}', [ChecklistController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('followup')->name('followup.')->group(function () {
        Route::get('/', [FollowupController::class, 'index'])->name('index');
        Route::post('/', [FollowupController::class, 'store'])->name('store');
        Route::get('/{followup}', [FollowupController::class, 'show'])->name('show');
        Route::get('/{followup}/attachment', [FollowupController::class, 'download'])->name('attachment');
        Route::post('/{followup}/approve', [FollowupController::class, 'approve'])->name('approve');
        Route::post('/{followup}/revision', [FollowupController::class, 'revision'])->name('revision');
    });

    Route::prefix('risk')->name('risk.')->group(function () {
        Route::get('/', [RiskController::class, 'index'])->name('index');
        Route::get('/{travel}', [RiskController::class, 'show'])->name('show');
        Route::post('/recalculate', [RiskController::class, 'recalculate'])->name('recalculate');
        Route::post('/recalculate/{travel}', [RiskController::class, 'recalculateTravel'])->name('recalculate.travel');
    });

    Route::prefix('compliance')->name('compliance.')->group(function () {
        Route::get('/', [ComplianceProfileController::class, 'index'])->name('index');
        Route::get('/{travel}', [ComplianceProfileController::class, 'show'])->name('show');
    });

    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::post('/read', [NotificationController::class, 'markRead'])->name('read');
        Route::post('/read-all', [NotificationController::class, 'markAllRead'])->name('read-all');
    });

    Route::prefix('audit-log')->name('audit-log.')->group(function () {
        Route::get('/', [AuditLogController::class, 'index'])->name('index');
        Route::get('/{auditLog}', [AuditLogController::class, 'show'])->name('show');
    });

    Route::prefix('export')->name('export.')->group(function () {
        Route::get('/travel', [ExportController::class, 'travel'])->name('travel');
        Route::get('/monitoring', [ExportController::class, 'monitoring'])->name('monitoring');
        Route::get('/pengawasan', [ExportController::class, 'pengawasan'])->name('pengawasan');
        Route::get('/dashboard', [ExportController::class, 'dashboard'])->name('dashboard');
    });
});
