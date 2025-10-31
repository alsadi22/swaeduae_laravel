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

// Phase 1: Social & Community Features
use App\Http\Controllers\Api\GroupController;
use App\Http\Controllers\Api\ActivityController;

// Phase 2: Advanced Search & Discovery
use App\Http\Controllers\Api\SearchController;
use App\Http\Controllers\Api\RecommendationController;
use App\Http\Controllers\Api\FavoriteController;

// Phase 5: Integrations & Third-Party Services
use App\Http\Controllers\Api\MessageController as ApiMessageController;
use App\Http\Controllers\Api\PaymentController as ApiPaymentController;
use App\Http\Controllers\Api\WalletController;

// Phase 6: AI & Personalization
use App\Http\Controllers\Api\PersonalizationController;
use App\Http\Controllers\Api\PredictionController;

// Phase 7: Analytics & Reporting
use App\Http\Controllers\Api\AnalyticsController;
use App\Http\Controllers\Api\ReportController;

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
    
    // Phase 1: Social & Community Features
    Route::prefix('social')->group(function () {
        Route::apiResource('groups', GroupController::class);
        Route::post('groups/{group}/join', [GroupController::class, 'join']);
        Route::post('groups/{group}/leave', [GroupController::class, 'leave']);
        Route::get('groups/{group}/members', [GroupController::class, 'members']);
        Route::post('groups/{group}/invite', [GroupController::class, 'invite']);
        Route::post('groups/{group}/invitations/{id}/accept', [GroupController::class, 'acceptInvitation']);
        Route::post('groups/{group}/invitations/{id}/reject', [GroupController::class, 'rejectInvitation']);
        
        Route::apiResource('activities', ActivityController::class);
        Route::get('feed', [ActivityController::class, 'feed']);
        Route::get('following-feed', [ActivityController::class, 'followingFeed']);
    });
    
    // Phase 2: Advanced Search & Discovery
    Route::prefix('search')->group(function () {
        Route::get('events', [SearchController::class, 'searchEvents']);
        Route::get('organizations', [SearchController::class, 'searchOrganizations']);
        Route::get('volunteers', [SearchController::class, 'searchVolunteers']);
        Route::get('suggestions', [SearchController::class, 'suggestions']);
        Route::post('save', [SearchController::class, 'saveSearch']);
        Route::get('saved', [SearchController::class, 'savedSearches']);
        Route::delete('saved/{id}', [SearchController::class, 'deleteSavedSearch']);
        Route::get('history', [SearchController::class, 'history']);
        Route::post('history/clear', [SearchController::class, 'clearHistory']);
    });
    
    Route::prefix('recommendations')->group(function () {
        Route::get('/', [RecommendationController::class, 'getRecommendations']);
        Route::get('for-me', [RecommendationController::class, 'forMe']);
        Route::get('trending', [RecommendationController::class, 'trending']);
    });
    
    Route::prefix('favorites')->group(function () {
        Route::get('/', [FavoriteController::class, 'index']);
        Route::post('/', [FavoriteController::class, 'store']);
        Route::delete('/{id}', [FavoriteController::class, 'destroy']);
        Route::post('/{id}/note', [FavoriteController::class, 'addNote']);
        Route::get('by-type/{type}', [FavoriteController::class, 'byType']);
        Route::get('count', [FavoriteController::class, 'count']);
    });
    
    // Phase 5: Integrations & Third-Party Services
    Route::prefix('messages')->group(function () {
        Route::get('conversations', [ApiMessageController::class, 'conversations']);
        Route::get('{userId}/messages', [ApiMessageController::class, 'messages']);
        Route::post('send', [ApiMessageController::class, 'send']);
        Route::post('{userId}/read', [ApiMessageController::class, 'markAsRead']);
        Route::get('unread-count', [ApiMessageController::class, 'unreadCount']);
        Route::delete('{id}', [ApiMessageController::class, 'delete']);
    });
    
    Route::prefix('notifications-settings')->group(function () {
        Route::get('preferences', [NotificationController::class, 'preferences']);
        Route::put('preferences', [NotificationController::class, 'updatePreferences']);
        Route::post('register-device', [NotificationController::class, 'registerDevice']);
        Route::delete('devices/{token}', [NotificationController::class, 'unregisterDevice']);
        Route::get('devices', [NotificationController::class, 'devices']);
        Route::post('test', [NotificationController::class, 'testNotification']);
    });
    
    Route::prefix('payments')->group(function () {
        Route::post('create-intent', [ApiPaymentController::class, 'createIntent']);
        Route::post('confirm', [ApiPaymentController::class, 'confirm']);
        Route::get('methods', [ApiPaymentController::class, 'getMethods']);
        Route::post('methods', [ApiPaymentController::class, 'addMethod']);
        Route::delete('methods/{id}', [ApiPaymentController::class, 'deleteMethod']);
        Route::post('refund', [ApiPaymentController::class, 'refund']);
        Route::get('history', [ApiPaymentController::class, 'history']);
        Route::get('{id}', [ApiPaymentController::class, 'show']);
    });
    
    Route::prefix('wallet')->group(function () {
        Route::get('summary', [WalletController::class, 'summary']);
        Route::get('balance', [WalletController::class, 'balance']);
        Route::get('transactions', [WalletController::class, 'transactions']);
        Route::post('add-balance', [WalletController::class, 'addBalance']);
        Route::post('deduct-balance', [WalletController::class, 'deductBalance']);
        Route::post('check-balance', [WalletController::class, 'checkBalance']);
    });
    
    // Phase 6: AI & Personalization
    Route::prefix('personalization')->group(function () {
        Route::get('recommendations', [PersonalizationController::class, 'recommendations']);
        Route::post('track-behavior', [PersonalizationController::class, 'trackBehavior']);
        Route::get('insights', [PersonalizationController::class, 'insights']);
        Route::post('recommendations/{id}/click', [PersonalizationController::class, 'trackRecommendationClick']);
        Route::post('recommendations/{id}/convert', [PersonalizationController::class, 'trackRecommendationConversion']);
        Route::get('ab-test/{testId}/results', [PersonalizationController::class, 'getTestResults']);
        Route::get('content/{contentType}/{contentId}', [PersonalizationController::class, 'getPersonalizedContent']);
    });
    
    Route::prefix('predictions')->group(function () {
        Route::get('churn-risk', [PredictionController::class, 'churnRisk']);
        Route::get('/', [PredictionController::class, 'predictions']);
        Route::post('conversion', [PredictionController::class, 'predictConversion']);
        Route::get('retention', [PredictionController::class, 'retentionScore']);
    });
    
    // Phase 7: Analytics & Reporting
    Route::prefix('analytics')->group(function () {
        Route::get('dashboard', [AnalyticsController::class, 'dashboard']);
        Route::get('kpi', [AnalyticsController::class, 'kpiByRange']);
        Route::get('trend', [AnalyticsController::class, 'metricTrend']);
        Route::get('growth', [AnalyticsController::class, 'growthRate']);
        Route::get('popular-pages', [AnalyticsController::class, 'popularPages']);
        Route::get('funnel', [AnalyticsController::class, 'conversionFunnel']);
        Route::get('geographic', [AnalyticsController::class, 'geographicDistribution']);
        Route::get('sessions', [AnalyticsController::class, 'userSessions']);
        Route::get('category', [AnalyticsController::class, 'kpiByCategory']);
        Route::get('target', [AnalyticsController::class, 'vsTarget']);
    });
    
    Route::prefix('reports')->group(function () {
        Route::get('/', [ReportController::class, 'index']);
        Route::post('/', [ReportController::class, 'store']);
        Route::get('{reportId}', [ReportController::class, 'show']);
        Route::post('{reportId}/generate', [ReportController::class, 'generate']);
        Route::get('{reportId}/instances', [ReportController::class, 'instances']);
        Route::get('cohort/{name}', [ReportController::class, 'cohortAnalysis']);
        Route::get('funnel/{name}', [ReportController::class, 'funnelAnalysis']);
        Route::post('{reportId}/publish', [ReportController::class, 'publish']);
        Route::post('{reportId}/schedule', [ReportController::class, 'schedule']);
    });
});