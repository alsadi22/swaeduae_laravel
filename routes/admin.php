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

// Admin Dashboard (protected by auth middleware)
Route::middleware(['auth', 'role:admin'])->group(function () {
    // Admin Dashboard
    Route::get('/admin', [DashboardController::class, 'index'])->name('admin.dashboard');

    // User Management
    Route::resource('users', UserController::class);

    // Organization Management
    Route::resource('organizations', OrganizationController::class);
    Route::post('organizations/{organization}/approve', [OrganizationController::class, 'approve'])->name('organizations.approve');
    Route::post('organizations/{organization}/reject', [OrganizationController::class, 'reject'])->name('organizations.reject');

    // Event Management
    Route::resource('events', EventController::class);
    Route::post('events/{event}/approve', [EventController::class, 'approve'])->name('events.approve');
    Route::post('events/{event}/reject', [EventController::class, 'reject'])->name('events.reject');

    // Application Management
    Route::resource('applications', ApplicationController::class);
    Route::post('applications/{application}/approve', [ApplicationController::class, 'approve'])->name('applications.approve');
    Route::post('applications/{application}/reject', [ApplicationController::class, 'reject'])->name('applications.reject');

    // Attendance Management
    Route::resource('attendance', AttendanceController::class);
    Route::get('attendance/event/{event}', [AttendanceController::class, 'eventAttendance'])->name('attendance.event');
    Route::post('attendance/{attendance}/verify', [AttendanceController::class, 'verify'])->name('attendance.verify');

    // Certificate Management
    Route::resource('certificates', CertificateController::class);
    Route::post('certificates/{certificate}/verify', [CertificateController::class, 'verify'])->name('certificates.verify');

    // Badge Management
    Route::resource('badges', BadgeController::class);
    Route::post('badges/{badge}/award', [BadgeController::class, 'award'])->name('badges.award');

    // Analytics
    Route::get('analytics', [AnalyticsController::class, 'index'])->name('analytics.index');
    Route::get('analytics/reports', [AnalyticsController::class, 'reports'])->name('analytics.reports');
    Route::post('analytics/export', [AnalyticsController::class, 'export'])->name('analytics.export');
    Route::post('analytics/generate-report', [AnalyticsController::class, 'generateReport'])->name('analytics.generate-report');
    Route::get('analytics/trends', [AnalyticsController::class, 'trends'])->name('analytics.trends');

    // Scheduled Reports
    Route::resource('scheduled-reports', ScheduledReportController::class);
    Route::post('scheduled-reports/{scheduledReport}/toggle-active', [ScheduledReportController::class, 'toggleActive'])->name('scheduled-reports.toggle-active');

    // Settings
    Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('settings', [SettingsController::class, 'update'])->name('settings.update');

    // Pages
    Route::prefix('admin')->group(function () {
        Route::resource('pages', PageController::class);
        Route::post('pages/{page}/toggle-published', [PageController::class, 'togglePublished'])->name('pages.toggle-published');
    });

    // Notification Management
    Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('notifications/broadcast', [NotificationController::class, 'broadcast'])->name('notifications.broadcast');
    Route::get('notifications/templates', [NotificationController::class, 'templates'])->name('notifications.templates');
    Route::get('notifications/logs', [NotificationController::class, 'logs'])->name('notifications.logs');
    Route::get('leaderboard', [NotificationController::class, 'leaderboard'])->name('leaderboard.index');
});