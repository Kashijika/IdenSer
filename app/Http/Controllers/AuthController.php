<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use App\Models\Role;
use App\Models\AuditLog;

class AuthController extends Controller
{
    /**
     * Show the login page
     */
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        
        return view('auth.login');
    }

    /**
     * Handle company account login
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        $credentials = $request->only('email', 'password');
        $remember = $request->has('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            
            // Update last login info
            $user = Auth::user();
            $user->update([
                'last_login_at' => now(),
                'last_login_ip' => $request->ip()
            ]);
            
            // Log the login
            AuditLog::log(
                'user_login',
                "User {$user->email} logged in successfully",
                'User',
                $user->id
            );
            
            return response()->json([
                'success' => true,
                'message' => 'Login successful',
                'redirect_url' => route('dashboard')
            ]);
        }

        // Log failed login attempt
        AuditLog::create([
            'user_id' => null,
            'action' => 'failed_login',
            'description' => "Failed login attempt for email: {$request->email}",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'status' => 'failed'
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Invalid credentials. Please check your email and password.'
        ], 401);
    }

    /**
     * Get authenticated user info
     */
    public function me(Request $request)
    {
        $user = Auth::user();
        $user->load('role');
        
        return response()->json([
            'success' => true,
            'user' => $user
        ]);
    }

    /**
     * Redirect to Google SSO
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle Google SSO callback
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            // Get default employee role
            $employeeRole = Role::where('name', 'employee')->first();
            
            $user = User::updateOrCreate([
                'email' => $googleUser->email,
            ], [
                'name' => $googleUser->name,
                'first_name' => explode(' ', $googleUser->name)[0] ?? $googleUser->name,
                'last_name' => explode(' ', $googleUser->name)[1] ?? '',
                'google_id' => $googleUser->id,
                'avatar' => $googleUser->avatar,
                'email_verified_at' => now(),
                'role_id' => $employeeRole?->id,
                'status' => 'active',
                'last_login_at' => now(),
                'last_login_ip' => request()->ip(),
            ]);

            Auth::login($user, true);

            // Log the SSO login
            AuditLog::log(
                'sso_login',
                "User {$user->email} logged in via Google SSO",
                'User',
                $user->id
            );

            return redirect()->route('dashboard');
        } catch (\Exception $e) {
            AuditLog::create([
                'user_id' => null,
                'action' => 'sso_login_failed',
                'description' => "Google SSO login failed: {$e->getMessage()}",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'status' => 'failed'
            ]);
            
            return redirect()->route('login')->with('error', 'Unable to login with Google. Please try again.');
        }
    }

    /**
     * Logout user
     */
    public function logout(Request $request)
    {
        $user = Auth::user();
        
        // Log the logout
        if ($user) {
            AuditLog::log(
                'user_logout',
                "User {$user->email} logged out",
                'User',
                $user->id
            );
        }
        
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Logged out successfully',
                'redirect_url' => route('login')
            ]);
        }

        return redirect()->route('login');
    }

    /**
     * Show password reset page
     */
    public function showPasswordReset()
    {
        return view('auth.password-reset');
    }

    /**
     * Show privacy policy page
     */
    public function showPrivacy()
    {
        return view('auth.privacy');
    }
}