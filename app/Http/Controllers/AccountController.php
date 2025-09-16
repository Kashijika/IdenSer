<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\WSO2Service;

class AccountController extends Controller
{
    protected $wso2Service;

    public function __construct(WSO2Service $wso2Service)
    {
        $this->wso2Service = $wso2Service;
    }

    /**
     * Redirect to account overview
     */
    public function index()
    {
        return redirect()->route('account.overview');
    }

    /**
     * Show account overview
     */
    public function overview()
    {
        $user = $this->wso2Service->getCurrentUser();
        if (!$user) {
            return redirect()->route('auth.login');
        }

        // Add role and initials for view compatibility (with error handling)
        try {
            $user['role_name'] = $this->wso2Service->getCurrentUserPrimaryRole();
            $user['initials'] = $this->wso2Service->getCurrentUserInitials();
        } catch (\Exception $e) {
            $user['role_name'] = 'Employee'; // Default role
            $user['initials'] = strtoupper(substr($user['email'] ?? 'User', 0, 1)); // Default initials from email
        }

        $profileCompletion = $this->calculateProfileCompletion($user);
        return view('account.overview', compact('user', 'profileCompletion'));
    }

    /**
     * Show personal info page
     */
    public function personalInfo()
    {
        $user = $this->wso2Service->getCurrentUser();
        if (!$user) {
            return redirect()->route('auth.login');
        }

        // Add role and initials for view compatibility (with error handling)
        try {
            $user['role_name'] = $this->wso2Service->getCurrentUserPrimaryRole();
            $user['initials'] = $this->wso2Service->getCurrentUserInitials();
        } catch (\Exception $e) {
            $user['role_name'] = 'Employee'; // Default role
            $user['initials'] = strtoupper(substr($user['email'] ?? 'User', 0, 1)); // Default initials from email
        }

        return view('account.personal-info', compact('user'));
    }

    /**
     * Show security page
     */
    public function security()
    {
        $user = $this->wso2Service->getCurrentUser();
        if (!$user) {
            return redirect()->route('auth.login');
        }

        // Add role and initials for view compatibility (with error handling)
        try {
            $user['role_name'] = $this->wso2Service->getCurrentUserPrimaryRole();
            $user['initials'] = $this->wso2Service->getCurrentUserInitials();
        } catch (\Exception $e) {
            $user['role_name'] = 'Employee'; // Default role
            $user['initials'] = strtoupper(substr($user['email'] ?? 'User', 0, 1)); // Default initials from email
        }

        return view('account.security', compact('user'));
    }

    /**
     * Update user profile (Note: This would need to be done via WSO2 API)
     */
    public function updateProfile(Request $request)
    {
        $user = $this->wso2Service->getCurrentUser();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated'
            ], 401);
        }
        
        $request->validate([
            'given_name' => 'required|string|max:255',
            'family_name' => 'required|string|max:255',
            'email' => 'required|email',
        ]);

        // Note: In a real implementation, you would update the user profile via WSO2 SCIM2 API
        // This is a placeholder that would need WSO2 API integration
        
        return response()->json([
            'success' => true,
            'message' => 'Profile update functionality needs WSO2 SCIM2 API integration'
        ]);
    }

    /**
     * Request role change (updated to work with WSO2)
     */
    public function requestRoleChange(Request $request)
    {
        $user = $this->wso2Service->getCurrentUser();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated'
            ], 401);
        }

        $request->validate([
            'requested_role_id' => 'required|exists:roles,id',
            'reason' => 'required|string|max:1000',
        ]);

        // Create role change request with WSO2 user info
        \App\Models\RoleChangeRequest::create([
            'wso2_user_id' => $user['id'],
            'wso2_user_email' => $user['email'],
            'wso2_user_name' => $user['name'],
            'requested_role_id' => $request->requested_role_id,
            'reason' => $request->reason,
            'status' => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Role change request submitted successfully'
        ]);
    }

    /**
     * Calculate profile completion percentage
     */
    private function calculateProfileCompletion($user)
    {
        $fields = ['given_name', 'family_name', 'email', 'username'];
        $completed = 0;
        
        foreach ($fields as $field) {
            if (!empty($user[$field])) {
                $completed++;
            }
        }
        
        return round(($completed / count($fields)) * 100);
    }
}