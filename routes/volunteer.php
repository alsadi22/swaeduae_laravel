<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Volunteer\DashboardController;
use App\Http\Controllers\Volunteer\ApplicationController;
use App\Http\Controllers\Volunteer\AttendanceController;
use App\Http\Controllers\Volunteer\CertificateController;
use App\Http\Controllers\Volunteer\BadgeController;
use App\Http\Controllers\Volunteer\ProfileController;
use App\Http\Controllers\Volunteer\NotificationController;
use App\Http\Controllers\Volunteer\GroupController;
use App\Http\Controllers\Volunteer\ActivityController;
use App\Http\Controllers\Volunteer\SearchController;
use App\Http\Controllers\Volunteer\RecommendationController;
use App\Http\Controllers\Volunteer\FavoriteController;
use App\Http\Controllers\Volunteer\PaymentController;
use App\Http\Controllers\Volunteer\WalletController;
use App\Http\Controllers\Volunteer\MessageController;

// Apply authentication middleware to all volunteer routes
Route::middleware(['auth', 'verified'])->group(function () {
    // Volunteer Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('volunteer.dashboard');

    // Applications
    Route::resource('applications', ApplicationController::class, ['names' => ['index' => 'volunteer.applications.index', 'create' => 'volunteer.applications.create', 'store' => 'volunteer.applications.store', 'show' => 'volunteer.applications.show', 'edit' => 'volunteer.applications.edit', 'update' => 'volunteer.applications.update', 'destroy' => 'volunteer.applications.destroy']]);

    // Attendance
    Route::resource('attendance', AttendanceController::class, ['names' => ['index' => 'volunteer.attendance.index', 'create' => 'volunteer.attendance.create', 'store' => 'volunteer.attendance.store', 'show' => 'volunteer.attendance.show', 'edit' => 'volunteer.attendance.edit', 'update' => 'volunteer.attendance.update', 'destroy' => 'volunteer.attendance.destroy']]);

    // Certificates
    Route::resource('certificates', CertificateController::class, ['names' => ['index' => 'volunteer.certificates.index', 'create' => 'volunteer.certificates.create', 'store' => 'volunteer.certificates.store', 'show' => 'volunteer.certificates.show', 'edit' => 'volunteer.certificates.edit', 'update' => 'volunteer.certificates.update', 'destroy' => 'volunteer.certificates.destroy']]);

    // Badges
    Route::resource('badges', BadgeController::class, ['names' => ['index' => 'volunteer.badges.index', 'create' => 'volunteer.badges.create', 'store' => 'volunteer.badges.store', 'show' => 'volunteer.badges.show', 'edit' => 'volunteer.badges.edit', 'update' => 'volunteer.badges.update', 'destroy' => 'volunteer.badges.destroy']]);

    // Profile
    Route::get('profile', [ProfileController::class, 'index'])->name('volunteer.profile.index');
    Route::put('profile', [ProfileController::class, 'update'])->name('volunteer.profile.update');

    // Notifications
    Route::get('notifications', [NotificationController::class, 'index'])->name('volunteer.notifications.index');
    Route::post('notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('volunteer.notifications.read');
    Route::post('notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('volunteer.notifications.read-all');
    Route::delete('notifications/{id}', [NotificationController::class, 'destroy'])->name('volunteer.notifications.destroy');
    Route::put('notifications/preferences', [NotificationController::class, 'updatePreferences'])->name('volunteer.notifications.update-preferences');
    
    // Phase 1: Social & Community Features
    Route::resource('groups', GroupController::class, ['names' => ['index' => 'volunteer.groups.index', 'create' => 'volunteer.groups.create', 'store' => 'volunteer.groups.store', 'show' => 'volunteer.groups.show', 'edit' => 'volunteer.groups.edit', 'update' => 'volunteer.groups.update', 'destroy' => 'volunteer.groups.destroy']]);
    Route::post('groups/{group}/join', [GroupController::class, 'join'])->name('volunteer.groups.join');
    Route::post('groups/{group}/leave', [GroupController::class, 'leave'])->name('volunteer.groups.leave');
    Route::get('groups/{group}/members', [GroupController::class, 'members'])->name('volunteer.groups.members');
    Route::post('groups/{group}/invite', [GroupController::class, 'invite'])->name('volunteer.groups.invite');
    Route::post('groups/{group}/invitations/{id}/accept', [GroupController::class, 'acceptInvitation'])->name('volunteer.invitations.accept');
    Route::post('groups/{group}/invitations/{id}/reject', [GroupController::class, 'rejectInvitation'])->name('volunteer.invitations.reject');
    
    Route::resource('activities', ActivityController::class, ['names' => ['index' => 'volunteer.activities.index', 'create' => 'volunteer.activities.create', 'store' => 'volunteer.activities.store', 'show' => 'volunteer.activities.show', 'edit' => 'volunteer.activities.edit', 'update' => 'volunteer.activities.update', 'destroy' => 'volunteer.activities.destroy']]);
    Route::get('feed', [ActivityController::class, 'feed'])->name('volunteer.activities.feed');
    Route::get('following-feed', [ActivityController::class, 'followingFeed'])->name('volunteer.activities.following');
    
    // Phase 2: Advanced Search & Discovery
    Route::get('search', [SearchController::class, 'index'])->name('search.index');
    Route::get('search/results', [SearchController::class, 'search'])->name('search.results');
    Route::get('search/suggestions', [SearchController::class, 'suggestions'])->name('search.suggestions');
    Route::post('search/save', [SearchController::class, 'saveSearch'])->name('search.save');
    Route::get('search/saved', [SearchController::class, 'savedSearches'])->name('search.saved');
    Route::delete('search/saved/{id}', [SearchController::class, 'deleteSavedSearch'])->name('search.delete-saved');
    Route::get('search/history', [SearchController::class, 'history'])->name('search.history');
    Route::post('search/history/clear', [SearchController::class, 'clearHistory'])->name('search.clear-history');
    
    Route::get('recommendations', [RecommendationController::class, 'index'])->name('recommendations.index');
    Route::get('recommendations/for-me', [RecommendationController::class, 'forMe'])->name('recommendations.for-me');
    Route::get('recommendations/trending', [RecommendationController::class, 'trending'])->name('recommendations.trending');
    
    Route::resource('favorites', FavoriteController::class, ['names' => ['index' => 'volunteer.favorites.index', 'create' => 'volunteer.favorites.create', 'store' => 'volunteer.favorites.store', 'show' => 'volunteer.favorites.show', 'edit' => 'volunteer.favorites.edit', 'update' => 'volunteer.favorites.update', 'destroy' => 'volunteer.favorites.destroy']]);
    Route::post('favorites/{id}/note', [FavoriteController::class, 'addNote'])->name('volunteer.favorites.note');
    Route::get('favorites/type/{type}', [FavoriteController::class, 'byType'])->name('volunteer.favorites.by-type');
    
    // Phase 5: Integrations & Third-Party Services
    Route::get('messages', [MessageController::class, 'conversations'])->name('messages.index');
    Route::get('messages/{userId}', [MessageController::class, 'show'])->name('messages.show');
    Route::post('messages/{userId}', [MessageController::class, 'store'])->name('messages.store');
    Route::get('messages/{userId}/unread', [MessageController::class, 'getUnreadCount'])->name('messages.unread');
    
    Route::prefix('notifications-settings')->group(function () {
        Route::get('/', [NotificationController::class, 'settings'])->name('notifications.settings');
        Route::post('preferences', [NotificationController::class, 'updatePreferences'])->name('notifications.update-prefs');
        Route::post('register-device', [NotificationController::class, 'registerDevice'])->name('notifications.register-device');
        Route::post('test', [NotificationController::class, 'testNotification'])->name('notifications.test');
    });
    
    Route::get('payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::post('payments/create-intent', [PaymentController::class, 'createIntent'])->name('payments.create-intent');
    Route::post('payments/confirm', [PaymentController::class, 'confirmPayment'])->name('payments.confirm');
    Route::get('payments/methods', [PaymentController::class, 'getPaymentMethods'])->name('payments.methods');
    Route::delete('payments/methods/{id}', [PaymentController::class, 'deleteMethod'])->name('payments.delete-method');
    Route::post('payments/set-default', [PaymentController::class, 'setDefault'])->name('payments.set-default');
    Route::get('payments/history', [PaymentController::class, 'history'])->name('payments.history');
    
    Route::get('wallet', [WalletController::class, 'index'])->name('wallet.index');
    Route::get('wallet/balance', [WalletController::class, 'getBalance'])->name('wallet.balance');
    Route::get('wallet/transactions', [WalletController::class, 'transactions'])->name('wallet.transactions');
    Route::post('wallet/add-balance', [WalletController::class, 'addBalance'])->name('wallet.add');
});