<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Organization\DashboardController;
use App\Http\Controllers\Organization\EventController;
use App\Http\Controllers\Organization\VolunteerController;
use App\Http\Controllers\Organization\AttendanceController;
use App\Http\Controllers\Organization\AnnouncementController;
use App\Http\Controllers\Organization\MessageController;
use App\Http\Controllers\Organization\EmergencyCommunicationController;
use App\Http\Controllers\Organization\CertificateController;
use App\Http\Controllers\Organization\ProfileController;

/*
|--------------------------------------------------------------------------
| Organization Routes
|--------------------------------------------------------------------------
|
| Here is where you can register organization routes for your application.
| These routes are loaded by the RouteServiceProvider within a group which
| contains the "organization" middleware group and role-based access control.
|
*/

Route::middleware(['auth', 'role:organization-manager,organization-staff'])->prefix('organization')->name('organization.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Event Management
    Route::resource('events', EventController::class);
    Route::post('events/store-step', [EventController::class, 'storeStep'])->name('events.store-step');
    Route::post('events/{event}/publish', [EventController::class, 'publish'])->name('events.publish');
    Route::post('events/{event}/unpublish', [EventController::class, 'unpublish'])->name('events.unpublish');
    
    // Volunteer Management
    Route::get('volunteers', [VolunteerController::class, 'index'])->name('volunteers.index');
    Route::get('volunteers/{volunteer}', [VolunteerController::class, 'show'])->name('volunteers.show');
    Route::post('volunteers/{volunteer}/approve', [VolunteerController::class, 'approve'])->name('volunteers.approve');
    Route::post('volunteers/{volunteer}/reject', [VolunteerController::class, 'reject'])->name('volunteers.reject');
    Route::post('volunteers/bulk-action', [VolunteerController::class, 'bulkAction'])->name('volunteers.bulk-action');
    
    // Attendance Management
    Route::get('attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::get('attendance/realtime/{event}', [AttendanceController::class, 'realtime'])->name('attendance.realtime');
    Route::get('attendance/{event}', [AttendanceController::class, 'show'])->name('attendance.show');
    Route::get('attendance/{event}/generate-qr', [AttendanceController::class, 'generateQrCodes'])->name('attendance.generate-qr');
    Route::post('attendance/checkin', [AttendanceController::class, 'checkin'])->name('attendance.checkin');
    Route::post('attendance/checkout', [AttendanceController::class, 'checkout'])->name('attendance.checkout');
    Route::post('attendance/{attendance}/verify', [AttendanceController::class, 'verify'])->name('attendance.verify');
    Route::get('attendance/{event}/export', [AttendanceController::class, 'export'])->name('attendance.export');
    
    // Announcements
    Route::prefix('events/{event}/announcements')->name('announcements.')->group(function () {
        Route::get('/', [AnnouncementController::class, 'index'])->name('index');
        Route::get('/create', [AnnouncementController::class, 'create'])->name('create');
        Route::post('/', [AnnouncementController::class, 'store'])->name('store');
        Route::get('/{announcement}/edit', [AnnouncementController::class, 'edit'])->name('edit');
        Route::put('/{announcement}', [AnnouncementController::class, 'update'])->name('update');
        Route::delete('/{announcement}', [AnnouncementController::class, 'destroy'])->name('destroy');
        Route::post('/{announcement}/publish', [AnnouncementController::class, 'publish'])->name('publish');
        Route::post('/{announcement}/unpublish', [AnnouncementController::class, 'unpublish'])->name('unpublish');
    });
    
    // Messages
    Route::prefix('events/{event}/messages')->name('messages.')->group(function () {
        Route::get('/', [MessageController::class, 'index'])->name('index');
        Route::post('/', [MessageController::class, 'store'])->name('store');
        Route::post('/{message}/read', [MessageController::class, 'markAsRead'])->name('read');
    });
    
    // Emergency Communications
    Route::prefix('events/{event}/emergency-communications')->name('emergency-communications.')->group(function () {
        Route::get('/', [EmergencyCommunicationController::class, 'index'])->name('index');
        Route::get('/create', [EmergencyCommunicationController::class, 'create'])->name('create');
        Route::post('/', [EmergencyCommunicationController::class, 'store'])->name('store');
        Route::get('/{emergencyCommunication}', [EmergencyCommunicationController::class, 'show'])->name('show');
        Route::delete('/{emergencyCommunication}', [EmergencyCommunicationController::class, 'destroy'])->name('destroy');
    });
    
    // Certificate Management
    Route::get('certificates', [CertificateController::class, 'index'])->name('certificates.index');
    Route::post('certificates/generate', [CertificateController::class, 'generate'])->name('certificates.generate');
    
    // Profile Management
    Route::get('profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');
});