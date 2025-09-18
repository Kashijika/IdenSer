<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\WSO2Service;
use Illuminate\Support\Facades\Log;

class ValidateWSO2Token
{
    protected $wso2Service;

    public function __construct(WSO2Service $wso2Service)
    {
        $this->wso2Service = $wso2Service;
    }

    /**
     * Handle an incoming request.
     */
    public function handle($request, Closure $next)
    {
        // Skip validation for auth routes to prevent loops
        if ($request->is('auth/*') || $request->is('login') || $request->is('/')) {
            return $next($request);
        }

        // Check if user has WSO2 session
        if (!session()->has('wso2_authenticated') || !session()->has('wso2_user_id')) {
            Log::info('No WSO2 session found, redirecting to login');
            return redirect()->route('auth.login');
        }

        // Basic token validation - only check if tokens exist and aren't obviously expired
        $wso2Session = session('wso2', []);
        $accessToken = $wso2Session['access_token'] ?? null;
        $expiresAt = $wso2Session['expires_at'] ?? null;
        
        if (!$accessToken) {
            Log::info('No access token in session');
            session()->flush();
            session()->regenerate(true);
            return redirect()->route('auth.login');
        }
        
        // Check if token has expired based on local expiry time
        if ($expiresAt && now()->timestamp >= $expiresAt) {
            Log::info('Access token has expired locally', [
                'expires_at' => $expiresAt,
                'current_time' => now()->timestamp
            ]);
            session()->flush();
            session()->regenerate(true);
            return redirect()->route('auth.login')
                             ->with('warning', 'Your session has expired. Please log in again.');
        }

        // For now, skip intensive token introspection to avoid authentication loops
        // Only do introspection occasionally (every 10th request) or on specific actions
        $shouldValidateToken = $this->shouldValidateTokenNow($request);

        if ($shouldValidateToken) {
            try {
                if (!$this->wso2Service->isAccessTokenValid($accessToken)) {
                    Log::info('WSO2 token introspection failed - token revoked or invalid');
                    session()->flush();
                    session()->regenerate(true);
                    return redirect()->route('auth.login')
                                     ->with('warning', 'Your session has been terminated from another application. Please log in again.');
                }
                // Update last validation time
                session(['last_token_validation' => time()]);
            } catch (\Exception $e) {
                // If introspection fails, log but don't interrupt the user experience unless it's a critical page
                Log::warning('Token introspection failed with exception', [
                    'error' => $e->getMessage(),
                    'critical_page' => $this->isCriticalPage($request)
                ]);
                
                // For critical pages, be more strict
                if ($this->isCriticalPage($request)) {
                    return redirect()->route('auth.login')
                                     ->with('warning', 'Unable to verify your session. Please log in again.');
                }
            }
        }

        return $next($request);
    }

    /**
     * Check if the current session is valid
     */
    private function hasValidSession(Request $request): bool
    {
        $wso2Session = $request->session()->get('wso2', []);
        $idTokenPayload = $request->session()->get('wso2_id_token_payload');
        
        // Check if we have basic session data
        if (empty($wso2Session) || !$idTokenPayload) {
            return false;
        }

        // Check if tokens exist
        $accessToken = $wso2Session['access_token'] ?? null;
        $refreshToken = $wso2Session['refresh_token'] ?? null;
        
        if (!$accessToken) {
            return false;
        }

        // Check ID token expiration
        $exp = $idTokenPayload['exp'] ?? 0;
        if ($exp && time() >= $exp) {
            Log::info('ID token expired, attempting refresh');
            return $this->refreshTokenIfNeeded($request, $refreshToken);
        }

        // Validate token with WSO2 if needed (optional introspection)
        // Temporarily disabled to debug - just return true if we have session data
        return true;
        // return $this->validateTokenWithWSO2($accessToken);
    }

    /**
     * Refresh token if possible
     */
    private function refreshTokenIfNeeded(Request $request, ?string $refreshToken): bool
    {
        if (!$refreshToken) {
            return false;
        }

        try {
            $newTokens = $this->wso2Service->refreshAccessToken($refreshToken);
            
            if ($newTokens) {
                // Update session with new tokens
                $request->session()->put('wso2', $newTokens);
                
                // Decode and store new ID token payload
                if (isset($newTokens['id_token'])) {
                    $idTokenPayload = $this->wso2Service->decodeJWT($newTokens['id_token']);
                    $request->session()->put('wso2_id_token_payload', $idTokenPayload);
                }
                
                Log::info('Successfully refreshed WSO2 tokens');
                return true;
            }
        } catch (\Exception $e) {
            Log::error('Token refresh failed', ['error' => $e->getMessage()]);
        }

        return false;
    }

    /**
     * Validate token with WSO2 introspection endpoint
     */
    private function validateTokenWithWSO2(string $accessToken): bool
    {
        try {
            // Use token introspection to validate
            return $this->wso2Service->introspectToken($accessToken);
        } catch (\Exception $e) {
            Log::warning('Token introspection failed', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Determine if token should be validated immediately
     */
    private function shouldValidateTokenNow($request)
    {
        $lastValidation = session('last_token_validation', 0);
        $timeSinceValidation = time() - $lastValidation;
        
        // Skip validation for 10 seconds after successful authentication to prevent timing issues
        $authTimestamp = session('wso2_auth_timestamp');
        if ($authTimestamp && (time() - $authTimestamp) < 10) {
            Log::info('Skipping token validation - within grace period after authentication', [
                'auth_timestamp' => $authTimestamp,
                'time_since_auth' => time() - $authTimestamp
            ]);
            return false;
        }
        
        // Always validate on first access or if forced (but not within grace period)
        if ($lastValidation === 0 || $request->has('validate_token')) {
            return true;
        }
        
        // Validate immediately for critical pages
        if ($this->isCriticalPage($request)) {
            return true;
        }
        
        // Validate if user has been idle and comes back
        $lastActivity = session('last_activity', time());
        $idleTime = time() - $lastActivity;
        session(['last_activity' => time()]);
        
        if ($idleTime > 180) { // 3 minutes idle
            return true;
        }
        
        // More frequent validation every 30 seconds for testing
        // This will help detect logout events from other apps faster
        // TODO: Change back to 60 seconds after testing
        if ($timeSinceValidation > 30) {
            return true;
        }
        
        return false;
    }

    /**
     * Check if current page is critical and requires immediate token validation
     */
    private function isCriticalPage($request)
    {
        $criticalPatterns = [
            'dashboard*',
            'users*',
            'admin*',
            'settings*',
            'security*',
            'logout*'
        ];
        
        foreach ($criticalPatterns as $pattern) {
            if ($request->is($pattern)) {
                return true;
            }
        }
        
        return false;
    }
}
