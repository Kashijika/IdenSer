<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\AuditLog;
use App\Services\WSO2Service;

class AuthController extends Controller
{
    protected $wso2Service;

    public function __construct(WSO2Service $wso2Service)
    {
        $this->wso2Service = $wso2Service;
    }

    /**
     * Show the login page
     */
    public function showLogin()
    {
        if (session()->has('wso2_user_id')) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    /**
     * Get authenticated user info
     */
    public function me(Request $request)
    {
        $user = $this->wso2Service->getCurrentUser();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated'
            ], 401);
        }

        // Get user roles from WSO2
        $roles = $this->wso2Service->getUserRoles($user['id']);
        $user['roles'] = $roles;

        return response()->json([
            'success' => true,
            'user' => $user
        ]);
    }

    /**
     * Redirect to WSO2 SSO
     */
    public function redirectToWSO2(Request $request)
    {
        $request->session()->put('oauth2_state', $state = Str::random(40));
        
        $query = [
            'response_type' => 'code',
            'client_id' => config('services.wso2.client_id'),
            'redirect_uri' => config('services.wso2.redirect_uri'),
            'scope' => 'openid email profile',
            'state' => $state,
        ];
        
        // Force fresh authentication if logout flag is set
        if ($request->session()->get('force_fresh_login')) {
            $query['prompt'] = 'login';
            $query['max_age'] = '0';
            $request->session()->forget('force_fresh_login');
            
            Log::info('Forcing fresh authentication after logout');
        }
        
        $authUrl = config('services.wso2.auth_url');
        return redirect($authUrl . '?' . http_build_query($query));
    }

    /**
     * Handle WSO2 SSO callback
     */
    public function handleWSO2Callback(Request $request)
    {
        try {
            // Exchange authorization code for access token
            $response = Http::withoutVerifying() 
                ->asForm()->post(config('services.wso2.token_url'), [
                    'grant_type' => 'authorization_code',
                    'code' => $request->code,
                    'redirect_uri' => config('services.wso2.redirect_uri'),
                    'client_id' => config('services.wso2.client_id'),
                ]);

            if ($response->failed()) {
                Log::error('WSO2 Token Exchange Failed', ['response' => $response->json()]);
                return redirect()->route('auth.login')->withErrors('Login failed. Please try again.');
            }

            $tokenData = $response->json();
            $request->session()->put('wso2', [
                'access_token'  => $tokenData['access_token'] ?? null,
                'id_token'      => $tokenData['id_token'] ?? null,
                'refresh_token' => $tokenData['refresh_token'] ?? null,
            ]);

            // Decode and store ID token payload
            $idToken = $tokenData['id_token'] ?? null;
            if ($idToken) {
                $payload = $this->wso2Service->parseJwtToken($idToken);
                if ($payload) {
                    $request->session()->put('wso2_id_token_payload', $payload);
                    $request->session()->put('wso2_user_id', $payload['sub']);
                    $request->session()->put('wso2_authenticated', true);
                }
            }

            // Log successful login
            $user = $this->wso2Service->getCurrentUser();
            if ($user) {
                AuditLog::create([
                    'wso2_user_id' => $user['id'],
                    'wso2_user_email' => $user['email'],
                    'wso2_user_name' => $user['name'],
                    'action' => 'sso_login',
                    'description' => "User {$user['email']} logged in via WSO2 SSO",
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'status' => 'success'
                ]);
            }

            return redirect()->route('dashboard');

        } catch (\Exception $e) {
            Log::error('WSO2 callback error', ['error' => $e->getMessage()]);
            return redirect()->route('auth.login')->with('error', 'An unexpected error occurred: ' . $e->getMessage());
        }
    }

    /**
     * Logout user
     */
    public function logout(Request $request)
    {
        // Log logout action
        $user = $this->wso2Service->getCurrentUser();
        if ($user) {
            try {
                AuditLog::create([
                    'wso2_user_id' => $user['id'],
                    'wso2_user_email' => $user['email'],
                    'wso2_user_name' => $user['name'],
                    'action' => 'logout',
                    'description' => "User {$user['email']} logged out",
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'status' => 'success'
                ]);
            } catch (\Exception $e) {
                Log::warning('Failed to log logout action', ['error' => $e->getMessage()]);
            }
        }

        // Get session data before clearing
        $wso2Session = $request->session()->get('wso2', []);
        $accessToken = $wso2Session['access_token'] ?? null;
        $refreshToken = $wso2Session['refresh_token'] ?? null;
        
        // Clear local session completely
        $request->session()->flush();
        $request->session()->regenerate(true);

        // Revoke tokens to invalidate them on WSO2 side
        if ($accessToken || $refreshToken) {
            try {
                $revokeUrl = config('services.wso2.base_url') . '/oauth2/revoke';
                
                if ($accessToken) {
                    Http::withoutVerifying()
                        ->asForm()
                        ->withBasicAuth(config('services.wso2.client_id'), config('services.wso2.client_secret'))
                        ->post($revokeUrl, [
                            'token' => $accessToken,
                            'token_type_hint' => 'access_token'
                        ]);
                }
                
                if ($refreshToken) {
                    Http::withoutVerifying()
                        ->asForm()
                        ->withBasicAuth(config('services.wso2.client_id'), config('services.wso2.client_secret'))
                        ->post($revokeUrl, [
                            'token' => $refreshToken,
                            'token_type_hint' => 'refresh_token'
                        ]);
                }
                
                Log::info('Tokens revoked during logout', [
                    'user_id' => $user['id'] ?? null
                ]);
                
            } catch (\Exception $e) {
                Log::warning('Token revocation failed during logout', ['error' => $e->getMessage()]);
            }
        }

        // Set a flag to force fresh authentication on next login
        $request->session()->put('force_fresh_login', true);

        Log::info('Logout completed - session cleared, tokens revoked');
        return redirect()->route('auth.login')->with('status', 'Successfully logged out');
    }
}
