<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\OrganizationController as ApiOrganizationController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ApplicationController;
use App\Http\Controllers\Api\CertificateController;
use App\Http\Controllers\Api\BadgeController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\MobileController;
use App\Http\Controllers\Api\LeaderboardController;

// Test route
Route::get('/test', function () {
    return response()->json(['message' => 'API is working']);
});

Route::get('/debug', function () {
    return 'Simple debug response';
});

// Public routes with rate limiting
Route::middleware(['throttle:5,1'])->group(function () {
    Route::post('/auth/login', [AuthController::class, 'login']);
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/auth/reset-password', [AuthController::class, 'resetPassword']);
});

// Public event routes with general rate limiting
Route::middleware(['throttle:60,1'])->group(function () {
    Route::get('/events', [EventController::class, 'index']);
    Route::get('/events/{event}', [EventController::class, 'show']);
    
    // Public organization routes
    Route::get('/organizations', [ApiOrganizationController::class, 'index']);
    Route::get('/organizations/{organization}', [ApiOrganizationController::class, 'show']);
    
    // Certificate verification (public)
    Route::post('/certificates/verify', [CertificateController::class, 'verify']);
});

// Protected routes with rate limiting
Route::middleware(['auth:sanctum', 'throttle:120,1'])->group(function () {
    // User profile
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::put('/user/profile', [AuthController::class, 'updateProfile']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    
    // User management
    Route::apiResource('users', UserController::class);
    Route::get('/users/{user}/applications', [UserController::class, 'applications']);
    Route::get('/users/{user}/attendance', [UserController::class, 'attendance']);
    Route::get('/users/{user}/certificates', [UserController::class, 'certificates']);
    Route::get('/users/{user}/badges', [UserController::class, 'badges']);
    Route::get('/users/{user}/statistics', [UserController::class, 'statistics']);
    Route::put('/users/{user}/notification-preferences', [UserController::class, 'updateNotificationPreferences']);
    Route::put('/users/{user}/privacy-settings', [UserController::class, 'updatePrivacySettings']);
    
    // Application management
    Route::apiResource('applications', ApplicationController::class);
    Route::get('/my-applications', [ApplicationController::class, 'myApplications']);
    Route::get('/organization-applications', [ApplicationController::class, 'organizationApplications']);
    Route::post('/applications/{application}/approve', [ApplicationController::class, 'approve']);
    Route::post('/applications/{application}/reject', [ApplicationController::class, 'reject']);
    
    // Certificate management
    Route::apiResource('certificates', CertificateController::class);
    Route::get('/my-certificates', [CertificateController::class, 'myCertificates']);
    Route::get('/users/{user}/public-certificates', [CertificateController::class, 'userPublicCertificates']);
    
    // Badge management
    Route::apiResource('badges', BadgeController::class);
    Route::get('/badges/{badge}/users', [BadgeController::class, 'users']);
    Route::post('/badges/{badge}/award', [BadgeController::class, 'awardToUser']);
    Route::get('/my-badges', [BadgeController::class, 'myBadges']);
    Route::get('/available-badges', [BadgeController::class, 'availableBadges']);
    
    // Notification management
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy']);
    Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount']);
    Route::get('/notifications/preferences', [NotificationController::class, 'getPreferences']);
    Route::put('/notifications/preferences', [NotificationController::class, 'updatePreferences']);
    
    // Attendance routes
    Route::prefix('attendance')->group(function () {
        // QR Code scanning
        Route::post('/scan', [AttendanceController::class, 'scan']);
        Route::post('/checkin', [AttendanceController::class, 'checkin']);
        Route::post('/checkout', [AttendanceController::class, 'checkout']);
        
        // Attendance history
        Route::get('/history', [AttendanceController::class, 'history']);
        Route::get('/{attendance}', [AttendanceController::class, 'show']);
        
        // Location validation
        Route::post('/validate-location', [AttendanceController::class, 'validateLocation']);
    });
    
    // Leaderboard and gamification routes
    Route::get('/leaderboard', [LeaderboardController::class, 'index']);
    Route::get('/progress', [LeaderboardController::class, 'progress']);
    Route::get('/progress/badge/{badgeId}', [LeaderboardController::class, 'badgeProgress']);
    
    // Mobile App API Routes
    Route::prefix('mobile')->group(function () {
        Route::get('/dashboard', [MobileController::class, 'dashboard']);
        Route::get('/events', [MobileController::class, 'events']);
        Route::get('/applications', [MobileController::class, 'applications']);
        Route::get('/attendance', [MobileController::class, 'attendance']);
        Route::post('/checkin', [MobileController::class, 'checkin']);
        Route::post('/checkout', [MobileController::class, 'checkout']);
        Route::get('/certificates', [MobileController::class, 'certificates']);
        Route::get('/badges', [MobileController::class, 'badges']);
        Route::get('/notifications', [MobileController::class, 'notifications']);
        Route::post('/notifications/{id}/read', [MobileController::class, 'markNotificationAsRead']);
    });
    
    // Event management (for organizations)
    Route::middleware('role:organization')->prefix('events')->group(function () {
        Route::post('/', [EventController::class, 'store']);
        Route::put('/{event}', [EventController::class, 'update']);
        Route::delete('/{event}', [EventController::class, 'destroy']);
        
        // Event attendance management
        Route::get('/{event}/attendance', [AttendanceController::class, 'eventAttendance']);
        Route::post('/{event}/qr-codes', [AttendanceController::class, 'generateQrCodes']);
        Route::put('/attendance/{attendance}/verify', [AttendanceController::class, 'verify']);
        Route::get('/{event}/attendance/export', [AttendanceController::class, 'export']);
    });
});