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
    if (!session()->has('wso2_user_id')) {
        return redirect()->route('auth.login');
    }
});

// Authentication Routes (WSO2 only)
Route::group(['prefix' => 'auth', 'as' => 'auth.'], function () {
    // Login page (GET)
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    
    // WSO2 SSO Routes only
    Route::get('/sso/wso2', [AuthController::class, 'redirectToWSO2'])->name('sso.wso2');
    Route::get('/sso/wso2/callback', [AuthController::class, 'handleWSO2Callback'])->name('sso.wso2.callback');
    
    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // Privacy policy
    Route::get('/privacy', [AuthController::class, 'showPrivacy'])->name('privacy');
});

// For backward compatibility with the login route name
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');

// Protected Dashboard Routes
Route::group([], function () {
    // Debug route to check user role and permissions
    Route::get('/debug-user', function () {
        try {
            $wso2Service = app(\App\Services\WSO2Service::class);
            $user = $wso2Service->getCurrentUser();
            if (!$user) {
                return response()->json(['error' => 'No user found']);
            }
            
            $debug = [
                'user' => $user,
                'role_name' => null,
                'initials' => null,
                'roles' => null,
                'permissions' => null,
                'session_data' => [
                    'wso2_user_id' => session('wso2_user_id'),
                    'user_role' => session('user_role'),
                    'access_token_exists' => session()->has('wso2_access_token')
                ]
            ];
            
            try {
                $debug['role_name'] = $wso2Service->getCurrentUserPrimaryRole();
            } catch (\Exception $e) {
                $debug['role_error'] = $e->getMessage();
            }
            
            try {
                $debug['initials'] = $wso2Service->getCurrentUserInitials();
            } catch (\Exception $e) {
                $debug['initials_error'] = $e->getMessage();
            }
            
            try {
                $debug['roles'] = $wso2Service->getUserRoles($user['id']);
            } catch (\Exception $e) {
                $debug['roles_error'] = $e->getMessage();
            }
            
            // Test actual permission checks with internal role names
            $testPermissions = [
                'can_access_users' => in_array($debug['role_name'] ?? '', ['admin', 'hr']),
                'can_access_roles' => in_array($debug['role_name'] ?? '', ['admin']),
                'can_access_trading_data' => in_array($debug['role_name'] ?? '', ['admin', 'hr', 'employee']),
                'can_access_security_policies' => in_array($debug['role_name'] ?? '', ['admin']),
                'can_access_audit_logs' => in_array($debug['role_name'] ?? '', ['admin', 'hr'])
            ];
            $debug['permission_tests'] = $testPermissions;
            
            return response()->json($debug, 200, [], JSON_PRETTY_PRINT);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()], 500);
        }
    });
    
    // Main Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Dashboard sections
    Route::group(['prefix' => 'dashboard', 'as' => 'dashboard.'], function () {
        Route::get('/users', [DashboardController::class, 'users'])->name('users');
        Route::get('/roles', [DashboardController::class, 'roles'])->name('roles');
        Route::get('/trading-data', [DashboardController::class, 'tradingData'])->name('trading-data');
        Route::get('/security-policies', [DashboardController::class, 'securityPolicies'])->name('security-policies');
        Route::get('/audit-logs', [DashboardController::class, 'auditLogs'])->name('audit-logs');
    Route::post('/security-policies/update', [DashboardController::class, 'updateSecurityPolicies'])->name('security-policies.update');
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
// Removed native Laravel auth middleware for account route. Use WSO2 session-based protection only.
// Route::middleware(['auth'])->get('/account', [AccountController::class, 'index'])->name('account');

// For backward compatibility and direct access to /account
Route::get('/account', [AccountController::class, 'index'])->name('account');

// API Routes for AJAX requests
Route::group(['prefix' => 'api'], function () {
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::put('/user/profile', [AccountController::class, 'updateProfile']);
    
    // Dashboard API endpoints
    Route::get('/dashboard/stats', [DashboardController::class, 'getDashboardStats']);
    Route::get('/trading-data/chart', [DashboardController::class, 'getTradingDataChart']);
});

Route::get('/session-set', function () {
    session(['foo' => 'bar']);
    return 'Session set';
});
Route::get('/session-get', function () {
    return session('foo', 'not set');
});

Route::get('reset-password/{token}', [AuthController::class, 'showPasswordReset'])
     ->name('password.reset');
// Removed native Laravel Auth routes to avoid session conflicts with WSO2 SSO.
// Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::view('/privacy', 'privacy')->name('privacy');


