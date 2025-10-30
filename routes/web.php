<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TestEmailController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\Auth\OrganizationRegistrationController;
use App\Http\Controllers\PageController as PublicPageController;

// Keep one simple test route for debugging if needed
Route::get('/debug-route', function () {
    return 'Route system is working!';
});

Route::get('/', function () {
    return view('welcome');
})->name('home');

// Public event routes
Route::get('/events', [EventController::class, 'index'])->name('events.index');
Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show');

// Public organization routes
Route::get('/organizations', [OrganizationController::class, 'index'])->name('organizations.index');
Route::get('/organizations/{organization}', [OrganizationController::class, 'show'])->name('organizations.show');

// Organization registration routes
Route::get('/organization/register', [OrganizationRegistrationController::class, 'create'])->name('organization.register');
Route::post('/organization/register', [OrganizationRegistrationController::class, 'store'])->name('organization.register.store');

// Public page routes
Route::get('/pages/{page}', [PublicPageController::class, 'show'])->name('pages.show');

// Organization pending approval
Route::get('/organizations/pending-approval', function () {
    return view('organizations.pending-approval');
})->middleware(['auth'])->name('organizations.pending-approval');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Test email route
Route::get('/test-email', [TestEmailController::class, 'sendTestEmail']);

require __DIR__.'/auth.php';
