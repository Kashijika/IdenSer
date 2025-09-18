<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\TradingData;
use App\Models\AuditLog;
use App\Models\RoleChangeRequest;
use App\Models\SecurityPolicy;
use App\Services\WSO2Service;

class DashboardController extends Controller
{
    protected $wso2Service;

    public function __construct(WSO2Service $wso2Service)
    {
        $this->wso2Service = $wso2Service;
    }

    /**
     * Show the main dashboard
     */
    public function index(Request $request)
    {
        // Get current user with error handling
        try {
            $user = $this->wso2Service->getCurrentUser();
            if (!$user) {
                return redirect()->route('auth.login');
            }
        } catch (\Exception $e) {
            Log::error('Failed to get current user in dashboard', ['error' => $e->getMessage()]);
            return redirect()->route('auth.login');
        }

        // Add role and initials for view compatibility (with error handling)
        try {
            $user['role_name'] = $this->wso2Service->getCurrentUserPrimaryRole();
            $user['initials'] = $this->wso2Service->getCurrentUserInitials();
        } catch (\Exception $e) {
            Log::warning('Failed to get user role/initials', ['error' => $e->getMessage()]);
            $user['role_name'] = 'employee'; // Default role (using internal name)
            $user['initials'] = substr($user['email'] ?? 'User', 0, 1); // Default initials from email
        }

        // Get WSO2 users count (this would need WSO2 API implementation)
        try {
            $wso2Roles = $this->wso2Service->getRoles(); // Get WSO2 roles instead of local roles
        } catch (\Exception $e) {
            Log::warning('Failed to get WSO2 roles', ['error' => $e->getMessage()]);
            $wso2Roles = []; // Default empty roles
        }
        
        // Calculate stats with error handling
        $stats = [
            'total_users'           => count($wso2Roles), // This is a placeholder - need proper WSO2 user count
            'total_roles'           => count($wso2Roles), // Use WSO2 roles count
            'pending_role_requests' => $this->safeModelCount(RoleChangeRequest::class, 'pending'),
            'recent_logins'         => $this->safeModelCount(AuditLog::class, 'sso_login'),
        ];

        // Get recent activity for dashboard (with error handling)
        try {
            $recentActivity = AuditLog::orderBy('created_at', 'desc')
                ->take(5)
                ->get();
        } catch (\Exception $e) {
            // Handle case where AuditLog table doesn't exist or has issues
            $recentActivity = collect([]);
        }

        // Get trading data for dashboard mini chart (with error handling)
        try {
            $tradingData = TradingData::orderBy('created_at', 'desc')
                ->take(8)
                ->get();
        } catch (\Exception $e) {
            // Handle case where TradingData table doesn't exist or has issues
            $tradingData = collect([]);
        }

        // Check if this is an AJAX request
        if ($request->ajax() || $request->has('ajax')) {
            return view('dashboard.index', compact('user', 'stats', 'recentActivity', 'tradingData'))->render();
        }

        return view('dashboard.index', compact('user', 'stats', 'recentActivity', 'tradingData'));
    }

    /**
     * Users management page (WSO2-based)
     */
    public function users(Request $request)
    {
        $user = $this->wso2Service->getCurrentUser();
        if (!$user) {
            return redirect()->route('auth.login');
        }

        // Add role and initials for view compatibility
        $user['role_name'] = $this->wso2Service->getCurrentUserPrimaryRole();
        $user['initials'] = $this->wso2Service->getCurrentUserInitials();

        // Check permissions via WSO2 roles - Based on SCIM2 Users API permissions
        $userRole = $user['role_name'] ?? '';
        
        // Admin: Has all SCIM2 Users API permissions (Create User, View User, Delete User, Update User, List Users)
        // Human Resources: Has all SCIM2 Users API permissions (Create User, View User, Delete User, Update User, List Users) 
        // Employee: Has limited SCIM2 Users API permissions (View User, List Users only)
        
        if (in_array($userRole, ['admin', 'hr'])) {
            // Admin and HR have full user management access based on their WSO2 permissions
            Log::info('User access granted to users section', [
                'user_id' => $user['id'],
                'role' => $userRole,
                'permissions' => 'Full SCIM2 Users API access'
            ]);
        } elseif ($userRole === 'employee') {
            // Employee has limited read-only access (View User, List Users)
            Log::info('Employee access granted to users section', [
                'user_id' => $user['id'],
                'role' => $userRole,
                'permissions' => 'Limited SCIM2 Users API access (View/List only)'
            ]);
        } else {
            Log::warning('Access denied to users section', [
                'user_id' => $user['id'],
                'role' => $userRole,
                'reason' => 'Role not recognized or no SCIM2 Users API permissions'
            ]);
            abort(403, 'Access denied. Your role (' . $userRole . ') does not have SCIM2 Users API permissions.');
        }

        // In a full implementation, you would fetch users from WSO2 SCIM2 API
        // For now, we'll show a placeholder with pagination
        $users = new \Illuminate\Pagination\LengthAwarePaginator(
            collect([]), // Empty collection of users
            0, // Total items
            20, // Items per page  
            1, // Current page
            [
                'path' => request()->url(),
                'pageName' => 'page',
            ]
        );

        // Get roles for the filter dropdown
        try {
            $wso2Roles = $this->wso2Service->getRoles();
            // Transform WSO2 roles to match view expectations
            $roles = collect($wso2Roles)->map(function ($role) {
                return (object) [
                    'id' => $role['id'] ?? uniqid(),
                    'name' => strtolower(str_replace(' ', '_', $role['displayName'] ?? '')),
                    'display_name' => $role['displayName'] ?? '',
                    'displayName' => $role['displayName'] ?? '',
                    'description' => $role['description'] ?? '',
                    'created_at' => now(), // WSO2 doesn't provide this
                    'permissions' => collect([]), // WSO2 structure is different
                    'users' => collect([]), // Would need separate API call
                ];
            });
        } catch (\Exception $e) {
            Log::warning('Failed to get roles for users page', ['error' => $e->getMessage()]);
            $roles = collect([]); // Default empty roles
        }

        // Get role change requests for the role requests tab
        try {
            $roleChangeRequests = RoleChangeRequest::with(['user'])
                ->where('status', 'pending')
                ->orderBy('created_at', 'desc')
                ->get();
        } catch (\Exception $e) {
            Log::warning('Failed to get role change requests', ['error' => $e->getMessage()]);
            $roleChangeRequests = collect([]); // Default empty collection
        }

        // Check if this is an AJAX request
        if ($request->ajax() || $request->has('ajax')) {
            return view('dashboard.users', compact('users', 'user', 'roles', 'roleChangeRequests'))->render();
        }

        return view('dashboard.users', compact('users', 'user', 'roles', 'roleChangeRequests'));
    }

    /**
     * Roles management page
     */
    public function roles(Request $request)
    {
        $user = $this->wso2Service->getCurrentUser();
        if (!$user) {
            return redirect()->route('auth.login');
        }

        // Add role and initials for view compatibility
        $user['role_name'] = $this->wso2Service->getCurrentUserPrimaryRole();
        $user['initials'] = $this->wso2Service->getCurrentUserInitials();

        // Check permissions via WSO2 roles - Based on SCIM2 Roles API permissions
        $userRole = $user['role_name'] ?? '';
        
        // Admin: Has all SCIM2 Roles API permissions (Update Role, Delete Role, Create Role, View Role)
        // Human Resources: Has limited SCIM2 Roles API permissions (Update Role, View Role only)
        // Employee: Has limited SCIM2 Roles API permissions (View Role only)
        
        if ($userRole === 'admin') {
            // Admin has full role management access based on WSO2 permissions
            Log::info('Admin access granted to roles section', [
                'user_id' => $user['id'],
                'role' => $userRole,
                'permissions' => 'Full SCIM2 Roles API access'
            ]);
        } elseif ($userRole === 'hr') {
            // HR has limited role management access (Update Role, View Role)
            Log::info('HR access granted to roles section', [
                'user_id' => $user['id'],
                'role' => $userRole,
                'permissions' => 'Limited SCIM2 Roles API access (Update/View only)'
            ]);
        } elseif ($userRole === 'employee') {
            // Employee has read-only role access (View Role only)
            Log::info('Employee access granted to roles section', [
                'user_id' => $user['id'],
                'role' => $userRole,
                'permissions' => 'Limited SCIM2 Roles API access (View only)'
            ]);
        } else {
            Log::warning('Access denied to roles section', [
                'user_id' => $user['id'],
                'role' => $userRole,
                'reason' => 'Role not recognized or no SCIM2 Roles API permissions'
            ]);
            abort(403, 'Access denied. Your role (' . $userRole . ') does not have SCIM2 Roles API permissions.');
        }

        // Get WSO2 roles and transform them for view compatibility
        try {
            $wso2Roles = $this->wso2Service->getRoles();
            // Transform WSO2 roles to match view expectations
            $wso2Roles = collect($wso2Roles)->map(function ($role) {
                return (object) [
                    'id' => $role['id'] ?? uniqid(),
                    'name' => strtolower(str_replace(' ', '_', $role['displayName'] ?? '')),
                    'display_name' => $role['displayName'] ?? '',
                    'displayName' => $role['displayName'] ?? '',
                    'description' => $role['description'] ?? 'WSO2 managed role',
                    'created_at' => now(), // WSO2 doesn't provide this
                    'permissions' => collect([]), // WSO2 structure is different
                    'users' => collect([]), // Would need separate API call
                ];
            });
        } catch (\Exception $e) {
            Log::warning('Failed to get WSO2 roles', ['error' => $e->getMessage()]);
            $wso2Roles = collect([]);
        }
        
        // Keep local roles for backward compatibility only if needed
        $localRoles = collect([]); // Remove local Role model dependency

        // Combine all roles for the view (rename wso2Roles to roles for view compatibility)
        $roles = $wso2Roles;

        // Check if this is an AJAX request
        if ($request->ajax() || $request->has('ajax')) {
            return view('dashboard.roles', compact('roles', 'localRoles', 'user'))->render();
        }

        return view('dashboard.roles', compact('roles', 'localRoles', 'user'));
    }

    /**
     * Trading data page
     */
    public function tradingData(Request $request)
    {
        $user = $this->wso2Service->getCurrentUser();
        if (!$user) {
            return redirect()->route('auth.login');
        }

        // Add role and initials for view compatibility
        $user['role_name'] = $this->wso2Service->getCurrentUserPrimaryRole();
        $user['initials'] = $this->wso2Service->getCurrentUserInitials();

        // Check permissions based on WSO2 roles - use internal role names
        $userRole = $user['role_name'] ?? '';
        $hasFullAccess = false;
        $hasLimitedAccess = false;

        if (in_array($userRole, ['admin', 'hr'])) {
            $hasFullAccess = true;
        } elseif ($userRole === 'employee') {
            $hasLimitedAccess = true;
        }

        if (!$hasFullAccess && !$hasLimitedAccess) {
            abort(403, 'Unauthorized access to trading data');
        }

        $query = TradingData::query();

        if ($hasLimitedAccess && !$hasFullAccess) {
            // Employee - limited access
            $query->where('created_at', '>=', now()->subDays(30));
        }

        $tradingData = $query->orderBy('created_at', 'desc')->paginate(20);

        // Calculate trading stats for the view
        $totalQuery = TradingData::query();
        if ($hasLimitedAccess && !$hasFullAccess) {
            $totalQuery->where('created_at', '>=', now()->subDays(30));
        }
        
        $stats = [
            'total_volume' => $totalQuery->sum('volume') ?: 0,
            'active_symbols' => $totalQuery->distinct('symbol')->count('symbol') ?: 0,
            'average_price' => $totalQuery->avg('price') ?: 0,
            'top_performer' => $totalQuery->orderBy('change_percent', 'desc')->value('symbol') ?: 'N/A',
            'top_performer_change' => $totalQuery->orderBy('change_percent', 'desc')->value('change_percent') ?: 0,
        ];

        // Generate chart data for the view
        $rawChartData = TradingData::getChartData();
        $chartData = [
            'price_points' => [],
            'labels' => [],
            'symbols' => [],
            'volume_data' => []
        ];

        if ($rawChartData->isNotEmpty()) {
            foreach ($rawChartData->take(8) as $index => $data) {
                $chartData['price_points'][] = [
                    'value' => (float) $data->price,
                    'symbol' => $data->symbol,
                    'date' => $data->trade_date->format('Y-m-d')
                ];
                $chartData['labels'][] = $data->symbol;
                $chartData['symbols'][] = $data->symbol;
                $chartData['volume_data'][] = [
                    'symbol' => $data->symbol,
                    'volume' => (int) $data->volume,
                    'date' => $data->trade_date->format('Y-m-d')
                ];
            }
        }

        // Check if this is an AJAX request
        if ($request->ajax() || $request->has('ajax')) {
            return view('dashboard.trading-data', compact('tradingData', 'hasFullAccess', 'user', 'stats', 'chartData'))->render();
        }

        return view('dashboard.trading-data', compact('tradingData', 'hasFullAccess', 'user', 'stats', 'chartData'));
    }

    /**
     * Security policies page
     */
    public function securityPolicies(Request $request)
    {
        $user = $this->wso2Service->getCurrentUser();
        if (!$user) {
            return redirect()->route('auth.login');
        }

        // Add role and initials for view compatibility
        $user['role_name'] = $this->wso2Service->getCurrentUserPrimaryRole();
        $user['initials'] = $this->wso2Service->getCurrentUserInitials();

        // Only admins can manage security policies
        $userRole = $user['role_name'] ?? '';
        if ($userRole !== 'admin') {
            abort(403, 'Unauthorized access to security policies');
        }

        // Get security policies and format them for the view
        $policies = SecurityPolicy::orderBy('created_at', 'desc')->get();
        
        // Create a consolidated security policy object for the view
        $securityPolicy = (object) [
            'password_complexity_enabled' => true,
            'min_password_length' => 8,
            'mfa_enabled' => false,
            'mfa_methods' => [],
            'session_timeout' => 60,
            'concurrent_sessions_enabled' => true,
            'max_concurrent_sessions' => 3,
            'account_lockout_enabled' => false,
            'max_login_attempts' => 5,
            'ip_whitelist_enabled' => false,
            'allowed_ips' => [],
            'audit_log_retention_days' => 90,
        ];

        // Override with actual policy settings if they exist
        foreach ($policies as $policy) {
            $settings = $policy->settings ?? [];
            
            switch ($policy->policy_name) {
                case 'password_policy':
                    $securityPolicy->password_complexity_enabled = $policy->is_enabled;
                    $securityPolicy->min_password_length = $settings['min_length'] ?? 8;
                    break;
                case 'mfa_policy':
                    $securityPolicy->mfa_enabled = $policy->is_enabled;
                    $securityPolicy->mfa_methods = $settings['methods'] ?? [];
                    break;
                case 'session_policy':
                    $securityPolicy->session_timeout = $settings['timeout_minutes'] ?? 60;
                    $securityPolicy->concurrent_sessions_enabled = $policy->is_enabled;
                    $securityPolicy->max_concurrent_sessions = $settings['max_sessions'] ?? 3;
                    break;
                case 'account_lockout_policy':
                    $securityPolicy->account_lockout_enabled = $policy->is_enabled;
                    $securityPolicy->max_login_attempts = $settings['max_attempts'] ?? 5;
                    break;
                case 'ip_whitelist_policy':
                    $securityPolicy->ip_whitelist_enabled = $policy->is_enabled;
                    $securityPolicy->allowed_ips = $settings['allowed_ips'] ?? [];
                    break;
                case 'audit_policy':
                    $securityPolicy->audit_log_retention_days = $settings['retention_days'] ?? 90;
                    break;
            }
        }

        // Calculate security score based on enabled policies
        $securityScore = 0;
        $maxScore = 100;
        
        // Password complexity (20 points)
        if ($securityPolicy->password_complexity_enabled) {
            $securityScore += 20;
        }
        
        // MFA enabled (25 points)
        if ($securityPolicy->mfa_enabled) {
            $securityScore += 25;
        }
        
        // Account lockout (15 points)
        if ($securityPolicy->account_lockout_enabled) {
            $securityScore += 15;
        }
        
        // Session timeout reasonable (15 points - if <= 120 minutes)
        if ($securityPolicy->session_timeout <= 120) {
            $securityScore += 15;
        }
        
        // Concurrent sessions controlled (10 points)
        if ($securityPolicy->concurrent_sessions_enabled && $securityPolicy->max_concurrent_sessions <= 5) {
            $securityScore += 10;
        }
        
        // IP whitelist (10 points)
        if ($securityPolicy->ip_whitelist_enabled) {
            $securityScore += 10;
        }
        
        // Audit retention (5 points - if >= 30 days)
        if ($securityPolicy->audit_log_retention_days >= 30) {
            $securityScore += 5;
        }

        // Check if this is an AJAX request
        if ($request->ajax() || $request->has('ajax')) {
            return view('dashboard.security-policies', compact('policies', 'securityPolicy', 'securityScore', 'user'))->render();
        }

        return view('dashboard.security-policies', compact('policies', 'securityPolicy', 'securityScore', 'user'));
    }

    /**
     * Update security policies
     */
    public function updateSecurityPolicies(Request $request)
    {
        // Validate and update security policies here
        // Example: $request->validate([...]);
        // SecurityPolicy::update([...]);
        // For now, just log and redirect back
        \Log::info('Security policies update requested', $request->all());
        return redirect()->route('dashboard.security-policies')->with('status', 'Security policies updated!');
    }

    /**
     * Audit logs page
     */
    public function auditLogs(Request $request)
    {
        $user = $this->wso2Service->getCurrentUser();
        if (!$user) {
            return redirect()->route('auth.login');
        }

        // Add role and initials for view compatibility
        $user['role_name'] = $this->wso2Service->getCurrentUserPrimaryRole();
        $user['initials'] = $this->wso2Service->getCurrentUserInitials();

        // Check audit log access permission - Admin and HR can view
        $userRole = $user['role_name'] ?? '';
        if (!in_array($userRole, ['admin', 'hr'])) {
            abort(403, 'Unauthorized access to audit logs');
        }

        $query = AuditLog::orderBy('created_at', 'desc');

        // Filter based on user role - if employee somehow gets here, limit access
        if ($userRole === 'employee') {
            // Employees can only see their own audit logs
            $query->where('wso2_user_id', $user['id']);
        }

        $auditLogs = $query->paginate(20);

        // Calculate audit log statistics
        $statsQuery = AuditLog::query();
        if ($userRole === 'employee') {
            $statsQuery->where('wso2_user_id', $user['id']);
        }
        
        $stats = [
            'success_count' => $statsQuery->where('action', 'like', '%success%')->orWhere('action', 'like', '%login%')->count(),
            'warning_count' => $statsQuery->where('action', 'like', '%warning%')->orWhere('action', 'like', '%failed%')->count(),
            'error_count' => $statsQuery->where('action', 'like', '%error%')->orWhere('action', 'like', '%unauthorized%')->count(),
            'unique_users' => $statsQuery->distinct('wso2_user_id')->count('wso2_user_id'),
        ];

        // Get all users for filter dropdown
        $users = $this->wso2Service->getAllUsers();

        // Check if this is an AJAX request
        if ($request->ajax() || $request->has('ajax')) {
            return view('dashboard.audit-logs', compact('auditLogs', 'user', 'users', 'stats'))->render();
        }

        return view('dashboard.audit-logs', compact('auditLogs', 'user', 'users', 'stats'));
    }

    /**
     * Get dashboard stats via API
     */
    public function getDashboardStats()
    {
        $user = $this->wso2Service->getCurrentUser();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $stats = [
            'total_roles'           => count($this->wso2Service->getRoles()), // Use WSO2 roles count
            'pending_role_requests' => RoleChangeRequest::pending()->count(),
            'recent_logins'         => AuditLog::where('action', 'sso_login')
                                          ->where('created_at', '>=', now()->subDays(7))
                                          ->count(),
            'total_audit_logs'      => AuditLog::count(),
        ];

        return response()->json($stats);
    }

    /**
     * Get trading data chart
     */
    public function getTradingDataChart()
    {
        $user = $this->wso2Service->getCurrentUser();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Check trading data access permission
        $roles = $this->wso2Service->getUserRoles($user['id']);
        $hasAccess = false;

        foreach ($roles as $role) {
            $roleName = $role['displayName'] ?? '';
            if (in_array($roleName, ['Admin', 'Human Resources', 'Employee'])) {
                $hasAccess = true;
                break;
            }
        }

        if (!$hasAccess) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $chartData = TradingData::getChartData();
        return response()->json($chartData);
    }

    /**
     * Safely count model records with error handling
     */
    private function safeModelCount($modelClass, $type = null)
    {
        try {
            if ($type === 'pending' && $modelClass === RoleChangeRequest::class) {
                return RoleChangeRequest::pending()->count();
            } elseif ($type === 'sso_login' && $modelClass === AuditLog::class) {
                return AuditLog::where('action', 'sso_login')
                    ->where('created_at', '>=', now()->subDays(7))
                    ->count();
            }
            return 0;
        } catch (\Exception $e) {
            return 0;
        }
    }
}
