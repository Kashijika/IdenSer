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
    public function showLogin(Request $request)
    {
        // Check if user is already authenticated
        if (session()->has('wso2_user_id')) {
            return redirect()->route('dashboard');
        }
        
        // Check if this is a post-logout redirect (force fresh login)
        if ($request->get('logged_out') == '1') {
            $request->session()->put('force_fresh_login', true);
            $request->session()->put('logout_timestamp', time());
            Log::info('Setting force_fresh_login flag due to logout redirect');
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
        
        // Always force fresh authentication after logout or if no session exists
        $shouldForceLogin = $request->session()->get('force_fresh_login') || 
                           !$request->session()->has('wso2_user_id') ||
                           $request->session()->get('logout_timestamp') ||
                           $request->get('logged_out') == '1'; // Also check URL parameter
        
        if ($shouldForceLogin) {
            $query['prompt'] = 'login';
            $query['max_age'] = '0';
            
            // Clear the flags after using them
            $request->session()->forget(['force_fresh_login', 'logout_timestamp']);
            
            Log::info('Forcing fresh authentication', [
                'has_force_flag' => $request->session()->get('force_fresh_login'),
                'has_logout_timestamp' => $request->session()->get('logout_timestamp'),
                'logged_out_param' => $request->get('logged_out'),
                'has_wso2_session' => $request->session()->has('wso2_user_id')
            ]);
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
            
            Log::info('WSO2 Token Response', ['tokenData' => $tokenData]);
            
            $request->session()->put('wso2', [
                'access_token'  => $tokenData['access_token'] ?? null,
                'id_token'      => $tokenData['id_token'] ?? null,
                'refresh_token' => $tokenData['refresh_token'] ?? null,
            ]);

            // Decode and store ID token payload
            $idToken = $tokenData['id_token'] ?? null;
            if ($idToken) {
                $payload = $this->wso2Service->parseJwtToken($idToken);
                Log::info('ID Token Payload', ['payload' => $payload]);
                if ($payload) {
                    $request->session()->put('wso2_id_token_payload', $payload);
                    $request->session()->put('wso2_user_id', $payload['sub']);
                    $request->session()->put('wso2_authenticated', true);
                    $request->session()->put('wso2_auth_timestamp', time()); // Add timestamp for middleware grace period
                    
                    Log::info('Session Data Set', [
                        'wso2_user_id' => $payload['sub'],
                        'wso2_authenticated' => true
                    ]);
                }
            }

            // Log successful login
            $user = $this->wso2Service->getCurrentUser();
            if ($user) {
                try {
                    AuditLog::create([
                        'user_id' => null, // No local user ID for WSO2 users
                        'action' => 'sso_login',
                        'entity_type' => 'user',
                        'entity_id' => $user['id'], // Store WSO2 user ID here
                        'description' => "WSO2 User {$user['email']} ({$user['name']}) logged in via SSO",
                        'ip_address' => $request->ip(),
                        'user_agent' => $request->userAgent(),
                        'status' => 'success'
                    ]);
                } catch (\Exception $e) {
                    Log::warning('Failed to log login action', ['error' => $e->getMessage()]);
                    // Continue with login even if audit logging fails
                }
            }

            return redirect()->route('dashboard');

        } catch (\Exception $e) {
            Log::error('WSO2 callback error', ['error' => $e->getMessage()]);
            return redirect()->route('auth.login')->with('error', 'An unexpected error occurred: ' . $e->getMessage());
        }
    }

    /**
     * Logout user with comprehensive token revocation and session invalidation
     */
    public function logout(Request $request)
    {
        // Log logout action
        $user = $this->wso2Service->getCurrentUser();
        if ($user) {
            try {
                AuditLog::create([
                    'user_id' => null, // No local user ID for WSO2 users
                    'action' => 'logout',
                    'entity_type' => 'user',
                    'entity_id' => $user['id'], // Store WSO2 user ID here
                    'description' => "WSO2 User {$user['email']} ({$user['name']}) initiated comprehensive logout",
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'status' => 'success'
                ]);
            } catch (\Exception $e) {
                Log::warning('Failed to log logout action', ['error' => $e->getMessage()]);
            }
        }

        // Get session data before clearing it
        $wso2Session = session('wso2', []);
        $accessToken = $wso2Session['access_token'] ?? null;
        $refreshToken = $wso2Session['refresh_token'] ?? null;
        $idToken = $wso2Session['id_token'] ?? null;
        
        // Step 1: Revoke all tokens to invalidate the WSO2 session
        $tokensRevoked = false;
        if ($accessToken || $refreshToken) {
            try {
                $tokensRevoked = $this->revokeAllWSO2Tokens($accessToken, $refreshToken);
                Log::info('WSO2 tokens revoked', ['success' => $tokensRevoked]);
            } catch (\Exception $e) {
                Log::warning('Failed to revoke WSO2 tokens', ['error' => $e->getMessage()]);
            }
        }

        // Step 2: Clear local session
        $request->session()->flush();
        $request->session()->regenerate(true);
        
        // Step 3: Set flags to force fresh login
        $request->session()->put('force_fresh_login', true);
        $request->session()->put('logout_timestamp', time());

        // Step 4: If we have tokens, also try WSO2 session termination endpoint
        if ($accessToken && $tokensRevoked) {
            try {
                $this->terminateWSO2UserSessions($user['id'] ?? null, $accessToken);
            } catch (\Exception $e) {
                Log::warning('Failed to terminate WSO2 user sessions', ['error' => $e->getMessage()]);
            }
        }

        Log::info('Comprehensive logout completed', [
            'user_id' => $user['id'] ?? null,
            'tokens_revoked' => $tokensRevoked,
            'has_access_token' => !empty($accessToken),
            'has_refresh_token' => !empty($refreshToken)
        ]);

        // For AJAX requests
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Logout successful - all sessions terminated',
                'redirect_url' => route('auth.login', ['logged_out' => 1])
            ]);
        }

        // Redirect back to login with logout flag
        return redirect()->route('auth.login', ['logged_out' => 1])
                         ->with('success', 'You have been logged out from all applications.');
    }

    /**
     * Revoke all WSO2 tokens to invalidate sessions across applications
     */
    private function revokeAllWSO2Tokens($accessToken, $refreshToken)
    {
        $revokeUrl = config('services.wso2.base_url') . '/oauth2/revoke';
        $clientId = config('services.wso2.client_id');
        $clientSecret = config('services.wso2.client_secret');
        $success = true;

        // Revoke access token
        if ($accessToken) {
            try {
                $response = Http::withoutVerifying()
                    ->asForm()
                    ->withBasicAuth($clientId, $clientSecret)
                    ->timeout(10)
                    ->post($revokeUrl, [
                        'token' => $accessToken,
                        'token_type_hint' => 'access_token'
                    ]);
                
                Log::info('Access token revocation', [
                    'status' => $response->status(),
                    'successful' => $response->successful(),
                    'body' => $response->body()
                ]);

                if (!$response->successful()) {
                    $success = false;
                }
            } catch (\Exception $e) {
                Log::warning('Failed to revoke access token', ['error' => $e->getMessage()]);
                $success = false;
            }
        }

        // Revoke refresh token
        if ($refreshToken) {
            try {
                $response = Http::withoutVerifying()
                    ->asForm()
                    ->withBasicAuth($clientId, $clientSecret)
                    ->timeout(10)
                    ->post($revokeUrl, [
                        'token' => $refreshToken,
                        'token_type_hint' => 'refresh_token'
                    ]);
                
                Log::info('Refresh token revocation', [
                    'status' => $response->status(),
                    'successful' => $response->successful(),
                    'body' => $response->body()
                ]);

                if (!$response->successful()) {
                    $success = false;
                }
            } catch (\Exception $e) {
                Log::warning('Failed to revoke refresh token', ['error' => $e->getMessage()]);
                $success = false;
            }
        }

        return $success;
    }

    /**
     * Terminate WSO2 user sessions using SCIM2 API
     */
    private function terminateWSO2UserSessions($userId, $accessToken)
    {
        if (!$userId || !$accessToken) {
            return false;
        }

        try {
            // Try to invalidate user sessions via WSO2 SCIM2 endpoint
            $baseUrl = config('services.wso2.base_url');
            $sessionEndpoint = $baseUrl . '/scim2/Me/sessions';
            
            $response = Http::withoutVerifying()
                ->withToken($accessToken)
                ->timeout(10)
                ->delete($sessionEndpoint);

            Log::info('WSO2 session termination attempt', [
                'user_id' => $userId,
                'endpoint' => $sessionEndpoint,
                'status' => $response->status(),
                'successful' => $response->successful(),
                'body' => $response->body()
            ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::warning('Failed to terminate WSO2 sessions via SCIM2', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
}
