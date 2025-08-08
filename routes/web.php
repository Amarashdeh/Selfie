<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\UserLoginController;
use App\Http\Controllers\Admin\Auth\LoginController as AdminLoginController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;

// Default landing page
Route::get('/', function () {
    return view('welcome');
});

// =====================
// Guest Login Routes
// =====================
Route::middleware('guest')->group(function () {
    // Admin Login Routes
    Route::get('/admin/login', [AdminLoginController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/admin/login', [AdminLoginController::class, 'login'])->name('admin.login.submit');

    // Users (Parent, Teacher, Moderator) Login Routes
    Route::get('/login', [UserLoginController::class, 'showLoginForm'])->name('user.login');
    Route::post('/login', [UserLoginController::class, 'login'])->name('user.login.submit');
});

// =====================
// Admin Routes
// =====================
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth:admin', 'admin', 'prevent-back-history'])
    ->group(function () {
        Route::post('/logout', [AdminLoginController::class, 'logout'])->name('logout');
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Add more admin routes here
    });

// =====================
// User Routes (Teacher, Moderator, Parent)
// =====================
Route::middleware(['auth:web', 'role:Teacher|Moderator|Parent', 'prevent-back-history'])->group(function () {
    Route::post('/logout', [UserLoginController::class, 'logout'])->name('user.logout');
    // Common dashboard route redirects to role-specific dashboards
    Route::get('/dashboard', function () {
        $role = auth()->user()->getRoleNames()->first();

        return match ($role) {
            'Teacher' => redirect()->route('teacher.dashboard'),
            'Moderator' => redirect()->route('moderator.dashboard'),
            'Parent' => redirect()->route('parent.dashboard'),
            default => abort(403),
        };
    })->name('dashboard');

    // Teacher dashboard
    Route::prefix('teacher')->middleware('role:Teacher')->group(function () {

        Route::get('/dashboard', fn () => view('teacher.dashboard'))->name('teacher.dashboard');
        // add more teacher routes here
    });

    // Moderator dashboard
    Route::prefix('moderator')->middleware('role:Moderator')->group(function () {
        Route::get('/dashboard', fn () => view('moderator.dashboard'))->name('moderator.dashboard');
        // add more moderator routes here
    });

    // Parent dashboard
    Route::prefix('parent')->middleware('role:Parent')->group(function () {
        Route::get('/dashboard', fn () => view('user.dashboard'))->name('parent.dashboard');
        // add more parent routes here
    });
});

// =====================
// Fallback Route (404)
// =====================
Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});


