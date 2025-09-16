<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class WSO2AuthMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip authentication check for auth routes
        if ($request->routeIs('auth.*')) {
            return $next($request);
        }

        // Check if user is authenticated via WSO2
        if (!session()->has('wso2_user_id') || !session()->has('wso2_authenticated') || !session()->has('wso2_id_token_payload')) {
            Log::info('WSO2 authentication required, redirecting to login', [
                'route' => $request->route()->getName(),
                'url' => $request->url(),
                'has_wso2_user_id' => session()->has('wso2_user_id'),
                'has_wso2_authenticated' => session()->has('wso2_authenticated'),
                'has_wso2_id_token_payload' => session()->has('wso2_id_token_payload')
            ]);
            
            return redirect()->route('auth.login');
        }

        return $next($request);
    }
}