<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Auth\UserLoginController;
use App\Http\Controllers\Admin\RoleManagementController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\Auth\LoginController as AdminLoginController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\LanguageController;
use App\Http\Controllers\Admin\TranslationController;

// =====================
// Default Landing Page
// =====================
Route::get('/', function () {
    return view('welcome');
});

// =====================
// Guest Routes (Login/Register)
// =====================
Route::middleware('guest')->group(function () {

    // Admin Login
    Route::get('/admin/login', [AdminLoginController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/admin/login', [AdminLoginController::class, 'login'])->name('admin.login.submit');

    // User Login (Parent, Teacher, Moderator)
    Route::get('/login', [UserLoginController::class, 'showLoginForm'])->name('user.login');
    Route::post('/login', [UserLoginController::class, 'login'])->name('user.login.submit');

    // User Registration
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store'])->name('register.store');

    // Password Reset
    Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
    Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('/reset-password', [NewPasswordController::class, 'store'])->name('password.update');
});

// =====================
// Admin Routes
// =====================
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth:admin', 'role:Admin|SuperAdmin', 'admin', 'prevent-back-history'])
    ->group(function () {

        Route::post('/logout', [AdminLoginController::class, 'logout'])->name('logout');
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Users Management
        Route::get('users', [UserManagementController::class, 'index'])->name('users.index');
        Route::get('users/data', [UserManagementController::class, 'data'])->name('users.data');
        Route::post('users', [UserManagementController::class, 'store'])->name('users.store');
        Route::get('users/{type}/{id}/edit', [UserManagementController::class, 'edit'])->name('users.edit');
        Route::put('users/{type}/{id}', [UserManagementController::class, 'update'])->name('users.update');
        Route::delete('users/{type}/{id}', [UserManagementController::class, 'destroy'])->name('users.destroy');
        Route::post('users/{type}/{id}/restore', [UserManagementController::class, 'restore'])->name('users.restore');
        Route::delete('users/{type}/{id}/force-delete', [UserManagementController::class,'forceDelete'])->name('users.forceDelete');

        // Roles Management
        Route::get('roles', [RoleManagementController::class, 'index'])->name('roles.index');
        Route::get('roles/data', [RoleManagementController::class, 'data'])->name('roles.data');
        Route::post('roles', [RoleManagementController::class, 'store'])->name('roles.store');
        Route::get('roles/{role}/edit', [RoleManagementController::class, 'edit'])->name('roles.edit');
        Route::put('roles/{role}', [RoleManagementController::class, 'update'])->name('roles.update');
        Route::delete('roles/{role}', [RoleManagementController::class, 'destroy'])->name('roles.destroy');

        // Settings
        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('/', [SettingController::class,'index'])->name('index');
            Route::post('/general', [SettingController::class,'saveGeneral'])->name('general');
            Route::post('/social', [SettingController::class,'saveSocial'])->name('social');
            Route::post('/livechat', [SettingController::class,'saveLiveChat'])->name('livechat');
            Route::post('/seo', [SettingController::class,'saveSeo'])->name('seo');
            Route::post('/twilio', [SettingController::class,'saveTwilio'])->name('twilio');
            Route::post('/email', [SettingController::class,'saveEmail'])->name('email');        
        });

        // Profile
        Route::get('profile', [ProfileController::class, 'myProfile'])->name('profile');
        Route::get('profile/edit', [ProfileController::class, 'editProfile'])->name('profile.edit');
        Route::post('profile/update', [ProfileController::class, 'updateProfile'])->name('profile.update');
        Route::post('profile/update-password', [ProfileController::class, 'updatePassword'])->name('profile.updatePassword');
        Route::post('profile/change-email', [ProfileController::class, 'changeEmail'])->name('profile.changeEmail');
        Route::get('verify-new-email/{id}/{hash}', [ProfileController::class, 'verifyNewEmail'])->name('profile.verifyNewEmail');
        Route::post('profile/request-phone-change', [ProfileController::class, 'requestPhoneChange'])->name('profile.requestPhoneChange');
        Route::post('profile/verify-phone-change', [ProfileController::class, 'verifyPhoneChange'])->name('profile.verifyPhoneChange');

        // Languages & Translations
        Route::resource('languages', LanguageController::class);
        Route::get('languages/{language}/terms', [TranslationController::class, 'editTerms'])->name('languages.editTerms');
        Route::post('languages/{language}/terms/store', [TranslationController::class, 'storeTerm'])->name('languages.storeTerm');
        Route::post('languages/{language}/terms/update/{term}', [TranslationController::class, 'updateTerm'])->name('languages.updateTerm');
        Route::delete('languages/{language}/terms/delete/{term}', [TranslationController::class, 'deleteTerm'])->name('languages.deleteTerm');
    });

// =====================
// User Routes (Parent/Teacher/Moderator)
// =====================


// Authenticated users routes
Route::middleware(['auth:web', 'role:Teacher|Moderator|Parent,web', 'prevent-back-history'])->group(function () {

    // Email verification notice (for logged-in users)
    Route::get('/verify-email', EmailVerificationPromptController::class)
        ->name('verification.notice');

    // Verification link from email
    Route::get('/verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    // Resend verification email
    Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware(['throttle:6,1'])
        ->name('verification.send');

    // Confirm Password
    Route::get('/confirm-password', [ConfirmablePasswordController::class, 'show'])->name('password.confirm');
    Route::post('/confirm-password', [ConfirmablePasswordController::class, 'store'])->name('password.confirm.store');



    // Logout
    Route::post('/logout', [UserLoginController::class, 'logout'])->name('user.logout');

    Route::middleware('verified')->group(function () {

        // Dashboard redirect based on role
        Route::get('/dashboard', function () {
            $user = auth('web')->user();
            if (!$user) abort(403, 'User is not logged in.');
            $role = $user->getRoleNames()->first();

            return match ($role) {
                'Teacher'   => redirect()->route('teacher.dashboard'),
                'Moderator' => redirect()->route('moderator.dashboard'),
                'Parent'    => redirect()->route('parent.dashboard'),
                default     => abort(403),
            };
        })->name('dashboard');

        // Teacher dashboard
        Route::prefix('teacher')->group(function () {
            Route::get('/dashboard', fn () => view('teacher.dashboard'))->name('teacher.dashboard');
        });

        // Moderator dashboard
        Route::prefix('moderator')->group(function () {
            Route::get('/dashboard', fn () => view('moderator.dashboard'))->name('moderator.dashboard');
        });

        // Parent dashboard
        Route::prefix('parent')->group(function () {
            Route::get('/dashboard', fn () => view('user.dashboard'))->name('parent.dashboard');
        });
    });
});

// =====================
// Fallback Route (404)
// =====================
Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});
