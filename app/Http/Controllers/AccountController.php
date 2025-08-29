<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AccountController extends Controller
{
    /**
     * Redirect to account overview
     */
    public function dashboard()
    {
        return redirect()->route('account.overview');
    }

    /**
     * Redirect to account overview
     */
    public function account()
    {
        return redirect()->route('account.overview');
    }

    /**
     * Show account overview
     */
    public function overview()
    {
        $user = Auth::user();
        $profileCompletion = $this->calculateProfileCompletion($user);
        
        return view('account.overview', compact('user', 'profileCompletion'));
    }

    /**
     * Show personal info page
     */
    public function personalInfo()
    {
        $user = Auth::user();
        return view('account.personal-info', compact('user'));
    }

    /**
     * Show security page
     */
    public function security()
    {
        $user = Auth::user();
        return view('account.security', compact('user'));
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'mobile' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
        ]);

        $user->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'name' => $request->first_name . ' ' . $request->last_name,
            'email' => $request->email,
            'mobile' => $request->mobile,
            'country' => $request->country,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully'
        ]);
    }

    /**
     * Calculate profile completion percentage
     */
    private function calculateProfileCompletion($user)
    {
        $fields = ['first_name', 'last_name', 'email', 'mobile', 'country'];
        $completed = 0;
        
        foreach ($fields as $field) {
            if (!empty($user->$field)) {
                $completed++;
            }
        }
        
        return round(($completed / count($fields)) * 100);
    }
}