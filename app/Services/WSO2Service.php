<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class WSO2Service
{
    private $baseUrl;
    private $clientId;
    private $clientSecret;
    private $scim2UsersUrl;
    private $scim2RolesUrl;

    public function __construct()
    {
        $this->baseUrl = config('services.wso2.base_url');
        $this->clientId = config('services.wso2.client_id');
        $this->clientSecret = config('services.wso2.client_secret');
        $this->scim2UsersUrl = config('services.wso2.scim2_users_url');
        $this->scim2RolesUrl = config('services.wso2.scim2_roles_url');
    }

    /**
     * Get access token for WSO2 API calls
     */
    public function getAccessToken()
    {
        $cacheKey = 'wso2_api_access_token';
        
        return Cache::remember($cacheKey, 3300, function () { // Cache for 55 minutes
            $response = Http::withoutVerifying()
                ->asForm()
                ->post($this->baseUrl . '/oauth2/token', [
                    'grant_type' => 'client_credentials',
                    'client_id' => $this->clientId,
                    'client_secret' => $this->clientSecret,
                    'scope' => 'internal_role_mgt_update internal_role_mgt_delete internal_role_mgt_create internal_role_mgt_view internal_user_mgt_create internal_user_mgt_view internal_user_mgt_delete internal_user_mgt_update internal_user_mgt_list'
                ]);

            if ($response->successful()) {
                return $response->json('access_token');
            }

            Log::error('Failed to get WSO2 access token', ['response' => $response->json()]);
            throw new \Exception('Failed to get WSO2 access token');
        });
    }

    /**
     * Get user details from WSO2 by user ID
     */
    public function getUser($userId)
    {
        try {
            $accessToken = $this->getAccessToken();
            $response = Http::withoutVerifying()
                ->withToken($accessToken)
                ->get($this->scim2UsersUrl . '/' . $userId);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Failed to get WSO2 user', [
                'user_id' => $userId,
                'response' => $response->json()
            ]);
            return null;
        } catch (\Exception $e) {
            Log::error('WSO2 get user error', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Get user by email
     */
    public function getUserByEmail($email)
    {
        try {
            $accessToken = $this->getAccessToken();
            $response = Http::withoutVerifying()
                ->withToken($accessToken)
                ->get($this->scim2UsersUrl, [
                    'filter' => "emails.value eq \"$email\""
                ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['Resources'][0] ?? null;
            }

            return null;
        } catch (\Exception $e) {
            Log::error('WSO2 get user by email error', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Get user roles from WSO2
     */
    public function getUserRoles($userId)
    {
        try {
            // First try to get user with roles included
            $accessToken = $this->getAccessToken();
            
            // Try multiple endpoints for getting user roles
            $endpoints = [
                $this->scim2UsersUrl . '/' . $userId,
                $this->scim2UsersUrl . '/' . $userId . '?attributes=roles,groups',
                $this->scim2UsersUrl . '/' . $userId . '?excludedAttributes=photos'
            ];
            
            foreach ($endpoints as $endpoint) {
                try {
                    $response = Http::withoutVerifying()
                        ->withToken($accessToken)
                        ->timeout(10)
                        ->get($endpoint);

                    if ($response->successful()) {
                        $user = $response->json();
                        $roles = $this->extractRolesFromUserData($user, $userId);
                        
                        if (!empty($roles)) {
                            Log::info('WSO2 user roles retrieved successfully', [
                                'user_id' => $userId,
                                'endpoint' => $endpoint,
                                'roles_count' => count($roles),
                                'roles' => $roles
                            ]);
                            return $roles;
                        }
                    }
                } catch (\Exception $endpointException) {
                    Log::warning('WSO2 endpoint failed', [
                        'endpoint' => $endpoint,
                        'error' => $endpointException->getMessage()
                    ]);
                    continue;
                }
            }

            // If direct user lookup fails, try getting roles via roles API
            return $this->getUserRolesViaRolesAPI($userId);
            
        } catch (\Exception $e) {
            Log::error('WSO2 get user roles error', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }

    /**
     * Extract roles from WSO2 user data
     */
    private function extractRolesFromUserData($user, $userId)
    {
        $roles = [];
        
        // Check different possible fields where roles might be stored
        if (isset($user['roles']) && is_array($user['roles'])) {
            $roles = $user['roles'];
        } elseif (isset($user['groups']) && is_array($user['groups'])) {
            // Sometimes roles are stored as groups
            $roles = $user['groups'];
        } elseif (isset($user['urn:ietf:params:scim:schemas:extension:enterprise:2.0:User']['roles'])) {
            // Enterprise extension format
            $roles = $user['urn:ietf:params:scim:schemas:extension:enterprise:2.0:User']['roles'];
        } elseif (isset($user['urn:scim:schemas:extension:wso2:1.0']['roles'])) {
            // WSO2 extension format
            $roles = $user['urn:scim:schemas:extension:wso2:1.0']['roles'];
        } else {
            // Log the user object structure for debugging
            Log::info('WSO2 user object structure', [
                'user_id' => $userId,
                'user_keys' => array_keys($user),
                'has_schemas' => isset($user['schemas']),
                'schemas' => $user['schemas'] ?? null
            ]);
        }

        return $roles;
    }

    /**
     * Get user roles by checking all roles and finding which ones include this user
     */
    private function getUserRolesViaRolesAPI($userId)
    {
        try {
            $allRoles = $this->getRoles();
            $userRoles = [];
            
            foreach ($allRoles as $role) {
                if (isset($role['users']) && is_array($role['users'])) {
                    foreach ($role['users'] as $user) {
                        if (isset($user['value']) && $user['value'] === $userId) {
                            $userRoles[] = [
                                'displayName' => $role['displayName'] ?? $role['name'] ?? 'Unknown Role',
                                'value' => $role['id'] ?? $role['value'] ?? null
                            ];
                            break;
                        }
                    }
                } elseif (isset($role['members']) && is_array($role['members'])) {
                    // Check members array (alternative structure)
                    foreach ($role['members'] as $member) {
                        if (isset($member['value']) && $member['value'] === $userId) {
                            $userRoles[] = [
                                'displayName' => $role['displayName'] ?? $role['name'] ?? 'Unknown Role',
                                'value' => $role['id'] ?? $role['value'] ?? null
                            ];
                            break;
                        }
                    }
                }
            }
            
            if (!empty($userRoles)) {
                Log::info('Found user roles via roles API', [
                    'user_id' => $userId,
                    'roles' => $userRoles
                ]);
            }
            
            return $userRoles;
            
        } catch (\Exception $e) {
            Log::error('Failed to get user roles via roles API', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }

    /**
     * Get all roles from WSO2
     */
    public function getRoles()
    {
        try {
            $accessToken = $this->getAccessToken();
            $response = Http::withoutVerifying()
                ->withToken($accessToken)
                ->get($this->scim2RolesUrl);

            if ($response->successful()) {
                return $response->json('Resources', []);
            }

            return [];
        } catch (\Exception $e) {
            Log::error('WSO2 get roles error', ['error' => $e->getMessage()]);
            return [];
        }
    }

    /**
     * Get role details by role name
     */
    public function getRoleByName($roleName)
    {
        try {
            $roles = $this->getRoles();
            foreach ($roles as $role) {
                if ($role['displayName'] === $roleName) {
                    return $role;
                }
            }
            return null;
        } catch (\Exception $e) {
            Log::error('WSO2 get role by name error', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Check if user has specific permission based on WSO2 roles
     */
    public function userHasPermission($userId, $permission)
    {
        $userRoles = $this->getUserRoles($userId);
        
        // Map WSO2 roles to permissions based on screenshots
        $rolePermissions = $this->getRolePermissionsMapping();
        
        foreach ($userRoles as $role) {
            $roleName = $role['display'] ?? $role['displayName'] ?? '';
            if (isset($rolePermissions[$roleName]) && in_array($permission, $rolePermissions[$roleName])) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Get role permissions mapping based on WSO2 screenshots
     */
    private function getRolePermissionsMapping()
    {
        return [
            'Admin' => [
                // SCIM2 Users API permissions
                'scim2_users_create_user',
                'scim2_users_view_user',
                'scim2_users_delete_user',
                'scim2_users_update_user',
                'scim2_users_list_users',
                // SCIM2 Roles API permissions
                'scim2_roles_update_role',
                'scim2_roles_delete_role',
                'scim2_roles_create_role',
                'scim2_roles_view_role',
                // Additional admin permissions
                'manage_security_policies',
                'view_audit_logs',
                'export_trading_data',
                'view_trading_data'
            ],
            'Human Resources' => [
                // SCIM2 Users API permissions (full access)
                'scim2_users_create_user',
                'scim2_users_view_user',
                'scim2_users_delete_user',
                'scim2_users_update_user',
                'scim2_users_list_users',
                // SCIM2 Roles API permissions (limited)
                'scim2_roles_update_role',
                'scim2_roles_view_role',
                // Additional HR permissions
                'view_trading_data',
                'change_user_roles'
            ],
            'Employee' => [
                // SCIM2 Users API permissions (limited)
                'scim2_users_create_user',
                'scim2_users_view_user',
                'scim2_users_delete_user',
                'scim2_users_update_user',
                'scim2_users_list_users',
                // No SCIM2 Roles API permissions by default
                // Limited permissions
                'view_own_profile',
                'edit_own_profile',
                'request_role_change',
                'view_limited_trading_data'
            ]
        ];
    }

    /**
     * Parse JWT token payload
     */
    public function parseJwtToken($token)
    {
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            return null;
        }

        try {
            $payload = json_decode(base64_decode(strtr($parts[1], '-_', '+/')), true);
            return $payload;
        } catch (\Exception $e) {
            Log::error('Failed to parse JWT token', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Get user info from session or WSO2
     */
    public function getCurrentUser()
    {
        $idTokenPayload = session('wso2_id_token_payload');
        if (!$idTokenPayload) {
            return null;
        }

        // Extract user info from ID token
        return [
            'id' => $idTokenPayload['sub'] ?? null,
            'email' => $idTokenPayload['email'] ?? null,
            'name' => $idTokenPayload['name'] ?? ($idTokenPayload['given_name'] . ' ' . $idTokenPayload['family_name']),
            'given_name' => $idTokenPayload['given_name'] ?? null,
            'family_name' => $idTokenPayload['family_name'] ?? null,
            'username' => $idTokenPayload['preferred_username'] ?? null,
        ];
    }

    /**
     * Get current user's primary role name (for UI compatibility)
     */
    public function getCurrentUserPrimaryRole()
    {
        $user = $this->getCurrentUser();
        if (!$user || !$user['id']) {
            return 'guest';
        }

        // First try to get roles from JWT token payload (more reliable)
        $idTokenPayload = session('wso2_id_token_payload');
        $roles = [];
        
        if ($idTokenPayload) {
            // Log the full JWT payload to understand its structure
            Log::info('Full JWT token payload', [
                'user_id' => $user['id'],
                'payload' => $idTokenPayload
            ]);
            
            // Check if roles are in the JWT token in various possible locations
            $possibleRoleLocations = [
                'groups', 'roles', 'authorities', 'permissions',
                'realm_access.roles', 'resource_access.*.roles',
                'wso2_roles', 'user_roles', 'application_roles'
            ];
            
            foreach ($possibleRoleLocations as $location) {
                $foundRoles = $this->extractRolesFromJWTLocation($idTokenPayload, $location);
                if (!empty($foundRoles)) {
                    $roles = $foundRoles;
                    Log::info('Found roles in JWT at location', [
                        'location' => $location,
                        'roles' => $roles
                    ]);
                    break;
                }
            }
            
            // Also check custom WSO2 claims that might contain roles
            foreach ($idTokenPayload as $claim => $value) {
                if (str_contains(strtolower($claim), 'role') && is_array($value)) {
                    $roles = array_map(function($role) {
                        return ['displayName' => is_string($role) ? $role : ($role['displayName'] ?? $role['name'] ?? 'Unknown')];
                    }, $value);
                    Log::info('Found roles in custom JWT claim', [
                        'claim' => $claim,
                        'roles' => $roles
                    ]);
                    break;
                }
            }
            
            // TEMPORARY: Check if we can extract roles from any other JWT claims
            // Look for common WSO2 role claim patterns
            $wso2RoleClaims = [
                'http://wso2.org/claims/role',
                'http://wso2.org/claims/roles',
                'application_roles',
                'user_roles',
                'wso2_roles'
            ];
            
            foreach ($wso2RoleClaims as $claim) {
                if (isset($idTokenPayload[$claim])) {
                    $claimValue = $idTokenPayload[$claim];
                    if (is_string($claimValue)) {
                        // Single role as string
                        $roles = [['displayName' => $claimValue]];
                        Log::info('Found role in WSO2 claim (string)', [
                            'claim' => $claim,
                            'role' => $claimValue
                        ]);
                        break;
                    } elseif (is_array($claimValue)) {
                        // Multiple roles as array
                        $roles = array_map(function($role) {
                            return ['displayName' => is_string($role) ? $role : ($role['displayName'] ?? $role['name'] ?? 'Unknown')];
                        }, $claimValue);
                        Log::info('Found roles in WSO2 claim (array)', [
                            'claim' => $claim,
                            'roles' => $roles
                        ]);
                        break;
                    }
                }
            }
            
            Log::info('Roles from JWT token', [
                'user_id' => $user['id'],
                'jwt_keys' => array_keys($idTokenPayload),
                'roles_found' => $roles
            ]);
        }
        
        // Fallback to SCIM2 API if no roles in JWT
        if (empty($roles)) {
            // Try using the user's access token for SCIM2 calls instead of client credentials
            $wso2Data = session('wso2');
            $accessToken = $wso2Data['access_token'] ?? null;
            
            if ($accessToken) {
                Log::info('Attempting to get roles via multiple methods', [
                    'user_id' => $user['id']
                ]);
                
                // Method 1: Try userinfo endpoint (often contains role information)
                $roles = $this->getUserRolesFromUserinfo($accessToken);
                
                // Method 2: Try SCIM2 API with user token
                if (empty($roles)) {
                    $roles = $this->getUserRolesWithToken($user['id'], $accessToken);
                }
                
                // Method 3: Try WSO2 Management API (alternative approach)
                if (empty($roles)) {
                    $roles = $this->getUserRolesFromManagementAPI($user['email']);
                }
            } else {
                // Fallback to client credentials SCIM2 API
                $roles = $this->getUserRoles($user['id']);
            }
        }

        // If no roles found from JWT or SCIM2 API, default to employee
        if (empty($roles)) {
            Log::info('No roles found for user, defaulting to employee', [
                'user_id' => $user['id'],
                'email' => $user['email'],
                'note' => 'This user may need roles assigned in WSO2 Identity Server'
            ]);
            return 'employee'; // Default role - all new users start as employee
        }

        // Define role priority and mapping (higher priority first)
        $roleMappings = [
            'admin' => ['Admin', 'admin'],
            'hr' => ['Human Resources', 'Human Relations', 'hr', 'HR'],
            'employee' => ['Employee', 'employee'],
        ];

        $rolePriority = [
            'admin' => 3,
            'hr' => 2,
            'employee' => 1,
        ];

        $highestRole = 'employee';
        $highestPriority = 0;

        foreach ($roles as $role) {
            $wso2RoleName = $role['displayName'] ?? $role['name'] ?? '';
            
            Log::info('Processing role', [
                'role_name' => $wso2RoleName,
                'role_data' => $role
            ]);
            
            // Find which application role this WSO2 role maps to
            $appRoleName = null;
            foreach ($roleMappings as $appRole => $wso2RoleNames) {
                if (in_array($wso2RoleName, $wso2RoleNames)) {
                    $appRoleName = $appRole;
                    break;
                }
            }
            
            if ($appRoleName) {
                $priority = $rolePriority[$appRoleName] ?? 0;
                if ($priority > $highestPriority) {
                    $highestPriority = $priority;
                    $highestRole = $appRoleName;
                }
                
                Log::info('Role mapped', [
                    'wso2_role' => $wso2RoleName,
                    'app_role' => $appRoleName,
                    'priority' => $priority
                ]);
            } else {
                Log::warning('Role not mapped', [
                    'wso2_role' => $wso2RoleName,
                    'available_mappings' => $roleMappings
                ]);
            }
        }

        Log::info('Final role assigned', [
            'user_id' => $user['id'],
            'final_role' => $highestRole,
            'priority' => $highestPriority
        ]);

        return $highestRole;
    }

    /**
     * Get current user initials for avatar
     */
    public function getCurrentUserInitials()
    {
        $user = $this->getCurrentUser();
        if (!$user) {
            return '?';
        }

        $givenName = $user['given_name'] ?? '';
        $familyName = $user['family_name'] ?? '';
        
        $initials = '';
        if ($givenName) {
            $initials .= strtoupper(substr($givenName, 0, 1));
        }
        if ($familyName) {
            $initials .= strtoupper(substr($familyName, 0, 1));
        }
        
        return $initials ?: '?';
    }

    /**
     * Get user roles from WSO2 Management API (alternative approach)
     * This tries to use basic auth or admin credentials to get role information
     */
    private function getUserRolesFromManagementAPI($userEmail)
    {
        try {
            Log::info('Attempting to get roles from WSO2 role configuration', [
                'user_email' => $userEmail
            ]);
            
            // Get role configuration
            $roleConfig = config('wso2_roles');
            $user = $this->getCurrentUser();
            
            if (!$roleConfig || !$user) {
                return [];
            }
            
            $roles = [];
            
            // First try user ID mapping (more specific)
            if (isset($roleConfig['user_roles_by_id'][$user['id']])) {
                $userRoles = $roleConfig['user_roles_by_id'][$user['id']];
                Log::info('Found user roles by ID in config', [
                    'user_id' => $user['id'],
                    'roles' => $userRoles
                ]);
            }
            // Fallback to email mapping
            elseif (isset($roleConfig['user_roles_by_email'][$userEmail])) {
                $userRoles = $roleConfig['user_roles_by_email'][$userEmail];
                Log::info('Found user roles by email in config', [
                    'user_email' => $userEmail,
                    'roles' => $userRoles
                ]);
            }
            // Use default role
            else {
                $userRoles = [$roleConfig['default_role'] ?? 'Employee'];
                Log::info('Using default role from config', [
                    'user_email' => $userEmail,
                    'default_role' => $userRoles[0]
                ]);
            }
            
            // Convert to proper format
            foreach ($userRoles as $role) {
                $roles[] = ['displayName' => $role];
            }
            
            return $roles;
            
        } catch (\Exception $e) {
            Log::error('Failed to get roles from role configuration', [
                'user_email' => $userEmail,
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }

    /**
     * Get user roles from WSO2 userinfo endpoint
     */
    private function getUserRolesFromUserinfo($accessToken)
    {
        try {
            $response = Http::withoutVerifying()
                ->withToken($accessToken)
                ->timeout(10)
                ->get(config('services.wso2.userinfo_url'));

            if ($response->successful()) {
                $userInfo = $response->json();
                
                Log::info('WSO2 userinfo response', [
                    'userinfo_keys' => array_keys($userInfo),
                    'userinfo_data' => $userInfo
                ]);
                
                // Check for roles in userinfo response
                $roles = [];
                $roleClaims = [
                    'roles', 'groups', 'authorities',
                    'http://wso2.org/claims/role',
                    'http://wso2.org/claims/roles',
                    'user_roles', 'application_roles'
                ];
                
                foreach ($roleClaims as $claim) {
                    if (isset($userInfo[$claim])) {
                        $claimValue = $userInfo[$claim];
                        if (is_string($claimValue)) {
                            $roles = [['displayName' => $claimValue]];
                        } elseif (is_array($claimValue)) {
                            $roles = array_map(function($role) {
                                return ['displayName' => is_string($role) ? $role : ($role['displayName'] ?? $role['name'] ?? 'Unknown')];
                            }, $claimValue);
                        }
                        
                        if (!empty($roles)) {
                            Log::info('Found roles in userinfo', [
                                'claim' => $claim,
                                'roles' => $roles
                            ]);
                            break;
                        }
                    }
                }
                
                return $roles;
            } else {
                Log::warning('WSO2 userinfo call failed', [
                    'status' => $response->status(),
                    'error' => $response->body()
                ]);
            }
            
            return [];
            
        } catch (\Exception $e) {
            Log::error('Failed to get roles from userinfo', [
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }

    /**
     * Get user roles using user's access token instead of client credentials
     */
    private function getUserRolesWithToken($userId, $accessToken)
    {
        try {
            // Try to get user details with the user's own access token
            $response = Http::withoutVerifying()
                ->withToken($accessToken)
                ->timeout(10)
                ->get($this->scim2UsersUrl . '/' . $userId);

            if ($response->successful()) {
                $user = $response->json();
                $roles = $this->extractRolesFromUserData($user, $userId);
                
                if (!empty($roles)) {
                    Log::info('WSO2 user roles retrieved with user token', [
                        'user_id' => $userId,
                        'roles_count' => count($roles),
                        'roles' => $roles
                    ]);
                    return $roles;
                }
            } else {
                Log::warning('SCIM2 API call failed with user token', [
                    'user_id' => $userId,
                    'status' => $response->status(),
                    'error' => $response->body()
                ]);
            }
            
            return [];
            
        } catch (\Exception $e) {
            Log::error('Failed to get user roles with user token', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }

    /**
     * Check if WSO2 user has specific role assigned
     */
    private function userHasWSO2Role($userId, $roleName)
    {
        $userRoles = $this->getUserRoles($userId);
        
        foreach ($userRoles as $role) {
            $roleDisplayName = $role['displayName'] ?? $role['name'] ?? '';
            if (strcasecmp($roleDisplayName, $roleName) === 0) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Extract roles from specific locations in JWT token
     */
    private function extractRolesFromJWTLocation($payload, $location)
    {
        $roles = [];
        
        switch ($location) {
            case 'groups':
                if (isset($payload['groups']) && is_array($payload['groups'])) {
                    $roles = array_map(function($group) {
                        return ['displayName' => $group];
                    }, $payload['groups']);
                }
                break;
                
            case 'roles':
                if (isset($payload['roles']) && is_array($payload['roles'])) {
                    $roles = array_map(function($role) {
                        return ['displayName' => is_string($role) ? $role : ($role['displayName'] ?? $role['name'] ?? 'Unknown')];
                    }, $payload['roles']);
                }
                break;
                
            case 'realm_access.roles':
                if (isset($payload['realm_access']['roles']) && is_array($payload['realm_access']['roles'])) {
                    $roles = array_map(function($role) {
                        return ['displayName' => $role];
                    }, $payload['realm_access']['roles']);
                }
                break;
                
            case 'resource_access.*.roles':
                if (isset($payload['resource_access']) && is_array($payload['resource_access'])) {
                    foreach ($payload['resource_access'] as $resource => $access) {
                        if (isset($access['roles']) && is_array($access['roles'])) {
                            $roles = array_map(function($role) {
                                return ['displayName' => $role];
                            }, $access['roles']);
                            break;
                        }
                    }
                }
                break;
                
            default:
                // Check if the location exists as a direct key
                if (isset($payload[$location]) && is_array($payload[$location])) {
                    $roles = array_map(function($role) {
                        return ['displayName' => is_string($role) ? $role : ($role['displayName'] ?? $role['name'] ?? 'Unknown')];
                    }, $payload[$location]);
                }
                break;
        }
        
        return $roles;
    }

    /**
     * Get all users from WSO2 for dropdown filters
     */
    public function getAllUsers()
    {
        try {
            $accessToken = $this->getAccessToken();
            
            $response = Http::withoutVerifying()
                ->withToken($accessToken)
                ->get($this->scim2UsersUrl);

            if ($response->successful()) {
                $data = $response->json();
                $users = [];
                
                if (isset($data['Resources']) && is_array($data['Resources'])) {
                    foreach ($data['Resources'] as $user) {
                        $users[] = [
                            'id' => $user['id'] ?? '',
                            'username' => $user['userName'] ?? '',
                            'email' => $user['emails'][0]['value'] ?? '',
                            'name' => $user['name']['givenName'] ?? '' . ' ' . $user['name']['familyName'] ?? '',
                        ];
                    }
                }
                
                return collect($users);
            }
            
            Log::warning('Failed to get users from WSO2', ['response' => $response->json()]);
            return collect([]);
            
        } catch (\Exception $e) {
            Log::error('Error getting users from WSO2', ['error' => $e->getMessage()]);
            return collect([]);
        }
    }

    /**
     * Refresh access token using refresh token
     */
    public function refreshAccessToken(string $refreshToken)
    {
        try {
            $response = Http::withoutVerifying()
                ->asForm()
                ->post(config('services.wso2.token_url'), [
                    'grant_type' => 'refresh_token',
                    'refresh_token' => $refreshToken,
                    'client_id' => $this->clientId,
                    'client_secret' => $this->clientSecret,
                ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Failed to refresh WSO2 token', ['response' => $response->json()]);
            return null;
        } catch (\Exception $e) {
            Log::error('Error refreshing WSO2 token', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Introspect token to check if it's valid
     */
    public function introspectToken(string $token): bool
    {
        try {
            $response = Http::withoutVerifying()
                ->asForm()
                ->withBasicAuth($this->clientId, $this->clientSecret)
                ->post($this->baseUrl . '/oauth2/introspect', [
                    'token' => $token,
                    'token_type_hint' => 'access_token'
                ]);

            if ($response->successful()) {
                $data = $response->json();
                return isset($data['active']) && $data['active'] === true;
            }

            return false;
        } catch (\Exception $e) {
            Log::error('Error introspecting token', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Decode JWT token payload
     */
    public function decodeJWT(string $token)
    {
        try {
            $parts = explode('.', $token);
            if (count($parts) !== 3) {
                throw new \Exception('Invalid JWT format');
            }

            $payload = $parts[1];
            $decoded = base64_decode(str_replace(['-', '_'], ['+', '/'], $payload));
            
            return json_decode($decoded, true);
        } catch (\Exception $e) {
            Log::error('Error decoding JWT', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Get WSO2 session check URL for cross-app session validation
     */
    public function getSessionCheckUrl(): string
    {
        return $this->baseUrl . '/oidc/checksession';
    }

    /**
     * Get WSO2 logout URL with proper post-logout redirect
     */
    public function getLogoutUrl(string $postLogoutRedirectUri = null): string
    {
        $logoutUrl = config('services.wso2.logout_url');
        
        if ($postLogoutRedirectUri) {
            $logoutUrl .= '?' . http_build_query([
                'post_logout_redirect_uri' => $postLogoutRedirectUri,
                'client_id' => $this->clientId
            ]);
        }
        
        return $logoutUrl;
    }

    /**
     * Perform single logout - revoke tokens and redirect to WSO2 logout
     */
    public function performSingleLogout(string $postLogoutRedirectUri = null): array
    {
        $wso2Session = session('wso2', []);
        $accessToken = $wso2Session['access_token'] ?? null;
        $refreshToken = $wso2Session['refresh_token'] ?? null;
        $idToken = $wso2Session['id_token'] ?? null;

        // Revoke tokens
        $tokensRevoked = false;
        if ($accessToken || $refreshToken) {
            $tokensRevoked = $this->revokeTokens($accessToken, $refreshToken);
        }

        // Clear local session
        session()->flush();
        session()->regenerate(true);

        // Build logout URL
        $logoutUrl = $this->getLogoutUrl($postLogoutRedirectUri);
        
        // Add id_token_hint for better logout experience
        if ($idToken) {
            $logoutUrl .= (strpos($logoutUrl, '?') !== false ? '&' : '?') . 'id_token_hint=' . urlencode($idToken);
        }

        return [
            'logout_url' => $logoutUrl,
            'tokens_revoked' => $tokensRevoked
        ];
    }

    /**
     * Revoke access and refresh tokens
     */
    private function revokeTokens(?string $accessToken, ?string $refreshToken): bool
    {
        $revokeUrl = $this->baseUrl . '/oauth2/revoke';
        $success = true;

        try {
            if ($accessToken) {
                $response = Http::withoutVerifying()
                    ->asForm()
                    ->withBasicAuth($this->clientId, $this->clientSecret)
                    ->post($revokeUrl, [
                        'token' => $accessToken,
                        'token_type_hint' => 'access_token'
                    ]);

                if (!$response->successful()) {
                    $success = false;
                    Log::warning('Failed to revoke access token');
                }
            }

            if ($refreshToken) {
                $response = Http::withoutVerifying()
                    ->asForm()
                    ->withBasicAuth($this->clientId, $this->clientSecret)
                    ->post($revokeUrl, [
                        'token' => $refreshToken,
                        'token_type_hint' => 'refresh_token'
                    ]);

                if (!$response->successful()) {
                    $success = false;
                    Log::warning('Failed to revoke refresh token');
                }
            }

            return $success;
        } catch (\Exception $e) {
            Log::error('Error revoking tokens', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Check if access token is valid and not revoked
     */
    public function isAccessTokenValid($token = null)
    {
        if (!$token) {
            $wso2Session = session('wso2', []);
            $token = $wso2Session['access_token'] ?? null;
        }

        if (!$token) {
            Log::info('No access token available for validation');
            return false;
        }

        try {
            // Use introspection endpoint to check token validity
            $introspectUrl = config('services.wso2.base_url') . '/oauth2/introspect';
            $clientId = config('services.wso2.client_id');
            $clientSecret = config('services.wso2.client_secret');

            $response = Http::withoutVerifying()
                ->asForm()
                ->withBasicAuth($clientId, $clientSecret)
                ->timeout(5) // Shorter timeout to prevent blocking
                ->retry(2, 100) // Retry twice with 100ms delay
                ->post($introspectUrl, [
                    'token' => $token,
                    'token_type_hint' => 'access_token'
                ]);

            if ($response->successful()) {
                $introspection = $response->json();
                $isActive = $introspection['active'] ?? false;
                
                Log::info('Token introspection result', [
                    'active' => $isActive,
                    'exp' => $introspection['exp'] ?? null,
                    'client_id' => $introspection['client_id'] ?? null
                ]);

                return $isActive;
            }

            Log::warning('Token introspection failed', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
            
            // If introspection fails due to server issues, assume token is valid
            // to prevent disrupting user experience
            if ($response->status() >= 500) {
                Log::info('WSO2 server error during introspection, assuming token is valid');
                return true;
            }
            
            return false;

        } catch (\Exception $e) {
            Log::error('Token validation error', [
                'error' => $e->getMessage(),
                'type' => get_class($e)
            ]);
            
            // Check if this is an HTTP 401 error specifically from introspection endpoint
            if ($e instanceof \Illuminate\Http\Client\RequestException) {
                $response = $e->response;
                if ($response && $response->status() === 401) {
                    // Parse the response to check if it's from introspection endpoint
                    $responseBody = $response->json();
                    
                    // Check for various invalid token response formats
                    if (isset($responseBody['error']) && $responseBody['error'] === 'invalid_token') {
                        Log::info('WSO2 introspection returned invalid_token - token is revoked');
                        return false;
                    } elseif (isset($responseBody['active']) && $responseBody['active'] === false) {
                        Log::info('WSO2 introspection returned active=false - token is revoked');
                        return false;
                    } elseif (isset($responseBody['code']) && $responseBody['code'] === 401) {
                        // WSO2 introspection endpoint returns this format for invalid tokens
                        Log::info('WSO2 introspection returned 401 code - token is invalid/revoked', [
                            'description' => $responseBody['description'] ?? 'No description'
                        ]);
                        return false;
                    } else {
                        Log::info('WSO2 returned 401 but not from introspection endpoint - assuming token valid');
                    }
                }
            }
            
            // On other network/timeout errors, assume token is valid to prevent disruption
            return true;
        }
    }

    /**
     * Validate current session and logout if token is invalid/revoked
     */
    public function validateSessionOrLogout()
    {
        $wso2Session = session('wso2', []);
        $accessToken = $wso2Session['access_token'] ?? null;

        if (!$accessToken) {
            Log::info('No access token in session - clearing session');
            $this->clearLocalSession();
            return false;
        }

        // Check if token is still valid
        if (!$this->isAccessTokenValid($accessToken)) {
            Log::info('Access token is invalid or revoked - clearing session');
            $this->clearLocalSession();
            return false;
        }

        return true;
    }

    /**
     * Clear local session data
     */
    private function clearLocalSession()
    {
        session()->flush();
        session()->regenerate(true);
        session()->put('force_fresh_login', true);
        session()->put('logout_timestamp', time());
    }
}
