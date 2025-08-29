<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AccountController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Redirect root to login
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

// Authentication Routes
Route::group(['prefix' => 'auth', 'as' => 'auth.'], function () {
    // Login page (GET)
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    
    // Login submission (POST)
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
    
    // SSO Routes
    Route::get('/sso/google', [AuthController::class, 'redirectToGoogle'])->name('sso.google');
    Route::get('/sso/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('sso.google.callback');
    
    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // Password reset pages
    Route::get('/password/reset', [AuthController::class, 'showPasswordReset'])->name('password.reset');
    Route::get('/privacy', [AuthController::class, 'showPrivacy'])->name('privacy');
});

// For backward compatibility with the login route name
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');

// Protected Dashboard Routes
Route::middleware(['auth'])->group(function () {
    // Main Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Dashboard sections
    Route::group(['prefix' => 'dashboard', 'as' => 'dashboard.'], function () {
        // Users Management
        Route::get('/users', [DashboardController::class, 'users'])->name('users');
        Route::post('/users', [DashboardController::class, 'storeUser'])->name('users.store');
        Route::put('/users/{user}', [DashboardController::class, 'updateUser'])->name('users.update');
        Route::delete('/users/{user}', [DashboardController::class, 'deleteUser'])->name('users.delete');
        
        // Role Change Requests
        Route::post('/role-requests/{request}/approve', [DashboardController::class, 'approveRoleRequest'])->name('role-requests.approve');
        Route::post('/role-requests/{request}/reject', [DashboardController::class, 'rejectRoleRequest'])->name('role-requests.reject');
        
        // Roles & Permissions Management  
        Route::get('/roles', [DashboardController::class, 'roles'])->name('roles');
        Route::post('/roles', [DashboardController::class, 'storeRole'])->name('roles.store');
        Route::put('/roles/{role}', [DashboardController::class, 'updateRole'])->name('roles.update');
        Route::delete('/roles/{role}', [DashboardController::class, 'deleteRole'])->name('roles.delete');
        
        // Trading Data
        Route::get('/trading-data', [DashboardController::class, 'tradingData'])->name('trading-data');
        Route::get('/trading-data/export', [DashboardController::class, 'exportTradingData'])->name('trading-data.export');
        
        // Security Policies
        Route::get('/security-policies', [DashboardController::class, 'securityPolicies'])->name('security-policies');
        Route::put('/security-policies/{policy}', [DashboardController::class, 'updateSecurityPolicy'])->name('security-policies.update');
        
        // Audit Logs
        Route::get('/audit-logs', [DashboardController::class, 'auditLogs'])->name('audit-logs');
    });
    
    // Account Management (existing functionality)
    Route::group(['prefix' => 'account', 'as' => 'account.'], function () {
        Route::get('/', [AccountController::class, 'index'])->name('index');
        Route::get('/overview', [AccountController::class, 'overview'])->name('overview');
        Route::get('/personal-info', [AccountController::class, 'personalInfo'])->name('personal-info');
        Route::get('/security', [AccountController::class, 'security'])->name('security');
        Route::put('/profile', [AccountController::class, 'updateProfile'])->name('profile.update');
        Route::put('/password', [AccountController::class, 'updatePassword'])->name('password.update');
        Route::post('/role-change-request', [AccountController::class, 'requestRoleChange'])->name('role-change-request');
    });
});

// For backward compatibility with the account route
Route::middleware(['auth'])->get('/account', [AccountController::class, 'index'])->name('account');

// API Routes for AJAX requests
Route::group(['prefix' => 'api', 'middleware' => 'auth'], function () {
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::put('/user/profile', [AccountController::class, 'updateProfile']);
    
    // Dashboard API endpoints
    Route::get('/dashboard/stats', [DashboardController::class, 'getDashboardStats']);
    Route::get('/trading-data/chart', [DashboardController::class, 'getTradingDataChart']);
});

Route::get('reset-password/{token}', [AuthController::class, 'showPasswordReset'])
     ->name('password.reset');
Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::view('/privacy', 'privacy')->name('privacy');
