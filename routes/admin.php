<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\OrganizationController;
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Admin\ApplicationController;
use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Controllers\Admin\CertificateController;
use App\Http\Controllers\Admin\BadgeController;
use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\ScheduledReportController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\PersonalizationAdminController;
use App\Http\Controllers\Admin\AnalyticsAdminController;

// Admin Dashboard (protected by auth middleware)
Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    // Admin Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('admin.dashboard');

    // User Management
    Route::resource('users', UserController::class, ['names' => ['index' => 'admin.users.index', 'create' => 'admin.users.create', 'store' => 'admin.users.store', 'show' => 'admin.users.show', 'edit' => 'admin.users.edit', 'update' => 'admin.users.update', 'destroy' => 'admin.users.destroy']]);

    // Organization Management
    Route::resource('organizations', OrganizationController::class, ['names' => ['index' => 'admin.organizations.index', 'create' => 'admin.organizations.create', 'store' => 'admin.organizations.store', 'show' => 'admin.organizations.show', 'edit' => 'admin.organizations.edit', 'update' => 'admin.organizations.update', 'destroy' => 'admin.organizations.destroy']]);
    Route::post('organizations/{organization}/approve', [OrganizationController::class, 'approve'])->name('admin.organizations.approve');
    Route::post('organizations/{organization}/reject', [OrganizationController::class, 'reject'])->name('admin.organizations.reject');

    // Event Management
    Route::resource('events', EventController::class, ['names' => ['index' => 'admin.events.index', 'create' => 'admin.events.create', 'store' => 'admin.events.store', 'show' => 'admin.events.show', 'edit' => 'admin.events.edit', 'update' => 'admin.events.update', 'destroy' => 'admin.events.destroy']]);
    Route::post('events/{event}/approve', [EventController::class, 'approve'])->name('admin.events.approve');
    Route::post('events/{event}/reject', [EventController::class, 'reject'])->name('admin.events.reject');

    // Application Management
    Route::resource('applications', ApplicationController::class, ['names' => ['index' => 'admin.applications.index', 'create' => 'admin.applications.create', 'store' => 'admin.applications.store', 'show' => 'admin.applications.show', 'edit' => 'admin.applications.edit', 'update' => 'admin.applications.update', 'destroy' => 'admin.applications.destroy']]);
    Route::post('applications/{application}/approve', [ApplicationController::class, 'approve'])->name('admin.applications.approve');
    Route::post('applications/{application}/reject', [ApplicationController::class, 'reject'])->name('admin.applications.reject');

    // Attendance Management
    Route::resource('attendance', AttendanceController::class, ['names' => ['index' => 'admin.attendance.index', 'create' => 'admin.attendance.create', 'store' => 'admin.attendance.store', 'show' => 'admin.attendance.show', 'edit' => 'admin.attendance.edit', 'update' => 'admin.attendance.update', 'destroy' => 'admin.attendance.destroy']]);
    Route::get('attendance/event/{event}', [AttendanceController::class, 'eventAttendance'])->name('admin.attendance.event');
    Route::post('attendance/{attendance}/verify', [AttendanceController::class, 'verify'])->name('admin.attendance.verify');

    // Certificate Management
    Route::resource('certificates', CertificateController::class, ['names' => ['index' => 'admin.certificates.index', 'create' => 'admin.certificates.create', 'store' => 'admin.certificates.store', 'show' => 'admin.certificates.show', 'edit' => 'admin.certificates.edit', 'update' => 'admin.certificates.update', 'destroy' => 'admin.certificates.destroy']]);
    Route::post('certificates/{certificate}/verify', [CertificateController::class, 'verify'])->name('admin.certificates.verify');

    // Badge Management
    Route::resource('badges', BadgeController::class, ['names' => ['index' => 'admin.badges.index', 'create' => 'admin.badges.create', 'store' => 'admin.badges.store', 'show' => 'admin.badges.show', 'edit' => 'admin.badges.edit', 'update' => 'admin.badges.update', 'destroy' => 'admin.badges.destroy']]);
    Route::post('badges/{badge}/award', [BadgeController::class, 'award'])->name('admin.badges.award');

    // Analytics
    Route::get('analytics', [AnalyticsController::class, 'index'])->name('analytics.index');
    Route::get('analytics/reports', [AnalyticsController::class, 'reports'])->name('analytics.reports');
    Route::post('analytics/export', [AnalyticsController::class, 'export'])->name('analytics.export');
    Route::post('analytics/generate-report', [AnalyticsController::class, 'generateReport'])->name('analytics.generate-report');
    Route::get('analytics/trends', [AnalyticsController::class, 'trends'])->name('analytics.trends');

    // Scheduled Reports
    Route::resource('scheduled-reports', ScheduledReportController::class, ['names' => ['index' => 'admin.scheduled-reports.index', 'create' => 'admin.scheduled-reports.create', 'store' => 'admin.scheduled-reports.store', 'show' => 'admin.scheduled-reports.show', 'edit' => 'admin.scheduled-reports.edit', 'update' => 'admin.scheduled-reports.update', 'destroy' => 'admin.scheduled-reports.destroy']]);
    Route::post('scheduled-reports/{scheduledReport}/toggle-active', [ScheduledReportController::class, 'toggleActive'])->name('admin.scheduled-reports.toggle-active');

    // Settings
    Route::get('settings', [SettingsController::class, 'index'])->name('admin.settings.index');
    Route::post('settings', [SettingsController::class, 'update'])->name('admin.settings.update');

    // Pages
    Route::resource('pages', PageController::class, ['names' => ['index' => 'admin.pages.index', 'create' => 'admin.pages.create', 'store' => 'admin.pages.store', 'show' => 'admin.pages.show', 'edit' => 'admin.pages.edit', 'update' => 'admin.pages.update', 'destroy' => 'admin.pages.destroy']]);
    Route::post('pages/{page}/toggle-published', [PageController::class, 'togglePublished'])->name('admin.pages.toggle-published');

    // Notification Management
    Route::get('notifications', [NotificationController::class, 'index'])->name('admin.notifications.index');
    Route::post('notifications/broadcast', [NotificationController::class, 'broadcast'])->name('admin.notifications.broadcast');
    Route::get('notifications/templates', [NotificationController::class, 'templates'])->name('admin.notifications.templates');
    Route::get('notifications/logs', [NotificationController::class, 'logs'])->name('admin.notifications.logs');
    Route::get('leaderboard', [NotificationController::class, 'leaderboard'])->name('admin.leaderboard.index');
    
    // Phase 5: Integrations & Third-Party Services (Admin)
    Route::prefix('integrations')->group(function () {
        Route::get('/', [AnalyticsController::class, 'integrations'])->name('integrations.index');
        Route::get('test/{name}', [AnalyticsController::class, 'testIntegration'])->name('integrations.test');
        Route::get('logs/{name}', [AnalyticsController::class, 'integrationLogs'])->name('integrations.logs');
    });
    
    Route::prefix('webhooks')->group(function () {
        Route::get('/', [AnalyticsController::class, 'webhooks'])->name('webhooks.index');
        Route::get('logs', [AnalyticsController::class, 'webhookLogs'])->name('webhooks.logs');
    });
    
    // Phase 6: AI & Personalization (Admin)
    Route::prefix('personalization')->group(function () {
        Route::get('dashboard', [PersonalizationAdminController::class, 'dashboard'])->name('personalization.dashboard');
        
        Route::prefix('feature-flags')->group(function () {
            Route::get('/', [PersonalizationAdminController::class, 'featureFlags'])->name('feature-flags.index');
            Route::post('/', [PersonalizationAdminController::class, 'createFeatureFlag'])->name('feature-flags.store');
            Route::post('{id}/toggle', [PersonalizationAdminController::class, 'toggleFeatureFlag'])->name('feature-flags.toggle');
            Route::put('{id}/rollout', [PersonalizationAdminController::class, 'updateRollout'])->name('feature-flags.update-rollout');
        });
        
        Route::prefix('ab-tests')->group(function () {
            Route::get('/', [PersonalizationAdminController::class, 'abTests'])->name('ab-tests.index');
            Route::post('/', [PersonalizationAdminController::class, 'createAbTest'])->name('ab-tests.store');
            Route::get('{id}/details', [PersonalizationAdminController::class, 'testDetails'])->name('ab-tests.details');
            Route::post('{id}/end', [PersonalizationAdminController::class, 'endTest'])->name('ab-tests.end');
            Route::get('{id}/winner', [PersonalizationAdminController::class, 'getWinner'])->name('ab-tests.winner');
        });
    });
    
    // Phase 7: Analytics & Reporting (Admin)
    Route::prefix('analytics-reports')->group(function () {
        Route::get('dashboard', [AnalyticsAdminController::class, 'dashboard'])->name('analytics-reports.dashboard');
        
        Route::prefix('kpis')->group(function () {
            Route::get('/', [AnalyticsAdminController::class, 'kpiManagement'])->name('kpis.index');
            Route::post('/', [AnalyticsAdminController::class, 'createKpi'])->name('kpis.store');
            Route::post('value', [AnalyticsAdminController::class, 'recordKpiValue'])->name('kpis.record-value');
            Route::get('{category}', [AnalyticsAdminController::class, 'analyticsBy'])->name('kpis.by-category');
        });
        
        Route::prefix('alerts')->group(function () {
            Route::get('/', [AnalyticsAdminController::class, 'alertManagement'])->name('alerts.index');
            Route::post('/', [AnalyticsAdminController::class, 'createAlert'])->name('alerts.store');
            Route::post('{id}/acknowledge', [AnalyticsAdminController::class, 'acknowledgeAlert'])->name('alerts.acknowledge');
            Route::post('{id}/resolve', [AnalyticsAdminController::class, 'resolveAlert'])->name('alerts.resolve');
        });
        
        Route::prefix('reports')->group(function () {
            Route::get('/', [AnalyticsAdminController::class, 'reportManagement'])->name('reports.index');
        });
    });
});