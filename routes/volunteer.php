<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Volunteer\DashboardController;
use App\Http\Controllers\Volunteer\ApplicationController;
use App\Http\Controllers\Volunteer\AttendanceController;
use App\Http\Controllers\Volunteer\CertificateController;
use App\Http\Controllers\Volunteer\BadgeController;
use App\Http\Controllers\Volunteer\ProfileController;
use App\Http\Controllers\Volunteer\NotificationController;

// Apply authentication middleware to all volunteer routes
Route::middleware(['auth', 'verified'])->group(function () {
    // Volunteer Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('volunteer.dashboard');

    // Applications
    Route::resource('applications', ApplicationController::class);

    // Attendance
    Route::resource('attendance', AttendanceController::class);

    // Certificates
    Route::resource('certificates', CertificateController::class);

    // Badges
    Route::resource('badges', BadgeController::class);

    // Profile
    Route::get('profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');

    // Notifications
    Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
    Route::delete('notifications/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
    Route::put('notifications/preferences', [NotificationController::class, 'updatePreferences'])->name('notifications.update-preferences');
});