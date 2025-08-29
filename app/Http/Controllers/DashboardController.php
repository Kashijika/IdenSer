<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Role;
use App\Models\TradingData;
use App\Models\AuditLog;
use App\Models\RoleChangeRequest;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the main dashboard
     */
    public function index()
    {
        $user = Auth::user();
        $user->load('role');

        // Dashboard statistics
        $stats = [
            'total_users' => User::active()->count(),
            'total_roles' => Role::count(),
            'pending_role_requests' => RoleChangeRequest::pending()->count(),
            'recent_logins' => User::whereNotNull('last_login_at')
                ->where('last_login_at', '>=', now()->subDays(7))
                ->count(),
        ];

        // Recent trading data for mini chart
        $tradingData = TradingData::getMarketSummary();

        // Recent activity logs (limited by role)
        $recentActivity = $this->getRecentActivity($user);

        return view('dashboard.index', compact('user', 'stats', 'tradingData', 'recentActivity'));
    }

    /**
     * Get recent activity based on user role
     */
    private function getRecentActivity($user)
    {
        $query = AuditLog::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(10);

        if ($user->isEmployee()) {
            // Employees can only see their own activity
            $query->where('user_id', $user->id);
        } elseif ($user->isHR()) {
            // HR can see user-related activities
            $query->whereIn('action', ['user_login', 'user_logout', 'profile_update', 'role_change_request']);
        }
        // Admins can see all activities (no additional filter)

        return $query->get();
    }

    /**
     * Show users management page
     */
    public function users(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->hasAnyPermission(['manage_users', 'change_user_roles'])) {
            abort(403, 'Unauthorized access');
        }

        $query = User::with('role')->where('id', '!=', $user->id);

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%");
            });
        }

        // Role filter
        if ($request->has('role') && $request->role) {
            $query->whereHas('role', function ($q) use ($request) {
                $q->where('name', $request->role);
            });
        }

        $users = $query->paginate(10);
        $roles = Role::all();

        // Pending role change requests (for HR/Admin)
        $pendingRequests = [];
        if ($user->hasPermission('manage_users')) {
            $pendingRequests = RoleChangeRequest::with(['user', 'requestedRole', 'currentRole'])
                ->pending()
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return view('dashboard.users', compact('users', 'roles', 'pendingRequests', 'user'));
    }

    /**
     * Show roles and permissions management page
     */
    public function roles()
    {
        $user = Auth::user();
        
        if (!$user->hasPermission('manage_roles')) {
            abort(403, 'Unauthorized access');
        }

        $roles = Role::withCount('users')->get();
        $availablePermissions = Role::getAvailablePermissions();

        return view('dashboard.roles', compact('roles', 'availablePermissions'));
    }

    /**
     * Show trading data page
     */
    public function tradingData(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->hasAnyPermission(['view_trading_data', 'view_limited_trading_data'])) {
            abort(403, 'Unauthorized access');
        }

        if ($user->hasPermission('view_trading_data')) {
            // Full access for Admin/HR
            $tradingData = TradingData::getChartData(null, 30);
            $marketSummary = TradingData::getMarketSummary();
            $canExport = $user->hasPermission('export_trading_data');
        } else {
            // Limited access for Employees
            $tradingData = TradingData::getLimitedData(7);
            $marketSummary = collect($tradingData)->take(3);
            $canExport = false;
        }

        return view('dashboard.trading-data', compact('tradingData', 'marketSummary', 'canExport', 'user'));
    }

    /**
     * Show security policies page
     */
    public function securityPolicies()
    {
        $user = Auth::user();
        
        if (!$user->hasPermission('manage_security_policies')) {
            abort(403, 'Unauthorized access');
        }

        $policies = \App\Models\SecurityPolicy::all();

        return view('dashboard.security-policies', compact('policies'));
    }

    /**
     * Show audit logs page
     */
    public function auditLogs(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->hasPermission('view_audit_logs')) {
            abort(403, 'Unauthorized access');
        }

        $query = AuditLog::with('user')->orderBy('created_at', 'desc');

        // Filters
        if ($request->has('action') && $request->action) {
            $query->where('action', $request->action);
        }

        if ($request->has('user_id') && $request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $auditLogs = $query->paginate(20);
        $users = User::select('id', 'name', 'email')->get();
        $actions = AuditLog::distinct('action')->pluck('action');

        return view('dashboard.audit-logs', compact('auditLogs', 'users', 'actions'));
    }
}