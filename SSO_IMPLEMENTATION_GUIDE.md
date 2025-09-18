# Single Sign-On (SSO) and Single Logout (SLO) Implementation Guide

## Overview

This document outlines the implementation of synchronized SSO and SLO between two Laravel applications (IdenSer and IdenSerper) using WSO2 Identity Server.

## Current Implementation

### 1. Token Validation Middleware
- **File**: `app/Http/Middleware/ValidateWSO2Token.php`
- **Purpose**: Automatically validates WSO2 access tokens and handles expired tokens
- **Features**:
  - Checks token expiration before processing requests
  - Attempts token refresh if expired
  - Automatically logs out users if token refresh fails
  - Redirects to login page with session cleared

### 2. Enhanced WSO2 Service
- **File**: `app/Services/WSO2Service.php`
- **New Methods**:
  - `validateAccessToken()`: Validates current token with WSO2
  - `refreshAccessToken()`: Refreshes expired tokens
  - `isTokenExpired()`: Checks if token is expired

### 3. Single Logout Implementation
- **File**: `app/Http/Controllers/AuthController.php`
- **Updated `logout()` method**:
  - Uses WSO2 SLO endpoint for centralized logout
  - Includes `post_logout_redirect_uri` parameter
  - Clears local session before redirecting

### 4. Route Protection
- **File**: `routes/web.php`
- **Protected Routes**:
  - All dashboard routes protected with `wso2.validate` middleware
  - Account management routes protected
  - API routes protected
  - Automatic token validation on every request

## Configuration Requirements

### WSO2 Environment Variables
Add to both applications' `.env` files:

```env
WSO2_BASE_URL=https://your-wso2-server.com
WSO2_CLIENT_ID=your_client_id
WSO2_CLIENT_SECRET=your_client_secret
WSO2_REDIRECT_URI=http://localhost:8000/auth/sso/wso2/callback
WSO2_AUTH_URL=https://your-wso2-server.com/oauth2/authorize
WSO2_TOKEN_URL=https://your-wso2-server.com/oauth2/token
WSO2_USERINFO_URL=https://your-wso2-server.com/oauth2/userinfo
WSO2_LOGOUT_URL=https://your-wso2-server.com/oidc/logout
WSO2_SCIM2_USERS_URL=https://your-wso2-server.com/scim2/Users
WSO2_SCIM2_ROLES_URL=https://your-wso2-server.com/scim2/Roles
```

### Cross-Application Configuration
For synchronized SSO/SLO, both applications must:

1. **Use the same WSO2 client configuration** or configured as trusted applications
2. **Share session domain** (if on same domain/subdomain)
3. **Configure proper logout redirect URIs** in WSO2

## Testing the Implementation

### 1. Single Sign-On Test
1. Log into IdenSer application
2. Navigate to IdenSerper application
3. Should be automatically authenticated without login prompt

### 2. Single Logout Test
1. While logged into both applications
2. Log out from either application
3. Verify that both applications show logged out state

### 3. Token Expiration Test
1. Log into application
2. Wait for token expiration or manually expire token
3. Navigate to any protected route
4. Should be automatically redirected to login

## Implementation Steps for IdenSerper

To implement the same SSO/SLO functionality in IdenSerper:

1. **Copy the middleware**: `ValidateWSO2Token.php`
2. **Update WSO2Service**: Add token validation methods
3. **Update AuthController**: Implement SLO logout method
4. **Register middleware**: In `bootstrap/app.php`
5. **Protect routes**: Apply `wso2.validate` middleware
6. **Configure environment**: Add WSO2 environment variables

## Security Considerations

1. **Token Security**: Tokens stored in secure sessions
2. **Cross-Application Trust**: Ensure proper WSO2 client configuration
3. **Session Management**: Proper session invalidation on logout
4. **Error Handling**: Graceful degradation on WSO2 service unavailability

## Troubleshooting

### Common Issues

1. **Token Refresh Fails**: Check WSO2 client permissions for refresh token flow
2. **Cross-App SSO Not Working**: Verify WSO2 client configuration and trusted applications
3. **Logout Not Synchronized**: Check WSO2 SLO endpoint configuration
4. **Session Issues**: Verify session configuration and domain settings

### Debug Tools

1. **Debug Route**: `/debug-user` - Shows current authentication state
2. **Session Routes**: `/session-get`, `/session-set` - For session debugging
3. **WSO2 Logs**: Check WSO2 server logs for authentication events

## Next Steps

1. **Deploy to IdenSerper**: Implement the same changes in the second application
2. **Configure WSO2**: Ensure proper client configuration for both applications
3. **Test Cross-App Flow**: Verify SSO and SLO work between applications
4. **Monitor Performance**: Add logging for authentication events
5. **Security Audit**: Review implementation for security best practices

## Files Modified

1. `app/Http/Middleware/ValidateWSO2Token.php` - Created
2. `app/Services/WSO2Service.php` - Enhanced with token validation
3. `app/Http/Controllers/AuthController.php` - Updated logout method
4. `bootstrap/app.php` - Registered middleware
5. `routes/web.php` - Applied middleware to protected routes
6. `config/services.php` - WSO2 configuration (already existed)
