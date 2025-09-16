<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'display_name',
        'description',
        'permissions',
        'is_system_role',
        'wso2_role_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'permissions' => 'array',
        'is_system_role' => 'boolean',
    ];

    /**
     * Get role change requests for this role.
     */
    public function roleChangeRequests()
    {
        return $this->hasMany(RoleChangeRequest::class, 'requested_role_id');
    }

    /**
     * Check if role has a specific permission.
     */
    public function hasPermission($permission)
    {
        return in_array($permission, $this->permissions ?? []);
    }

    /**
     * Add permission to role.
     */
    public function addPermission($permission)
    {
        $permissions = $this->permissions ?? [];
        if (!in_array($permission, $permissions)) {
            $permissions[] = $permission;
            $this->permissions = $permissions;
            $this->save();
        }
    }

    /**
     * Remove permission from role.
     */
    public function removePermission($permission)
    {
        $permissions = $this->permissions ?? [];
        $permissions = array_filter($permissions, function ($p) use ($permission) {
            return $p !== $permission;
        });
        $this->permissions = array_values($permissions);
        $this->save();
    }

    /**
     * Get available WSO2 SCIM2 permissions list based on screenshots.
     */
    public static function getAvailablePermissions()
    {
        return [
            // SCIM2 Users API permissions
            'scim2_users_create_user' => 'Create User',
            'scim2_users_view_user' => 'View User',
            'scim2_users_delete_user' => 'Delete User',
            'scim2_users_update_user' => 'Update User',
            'scim2_users_list_users' => 'List Users',
            
            // SCIM2 Roles API permissions  
            'scim2_roles_update_role' => 'Update Role',
            'scim2_roles_delete_role' => 'Delete Role',
            'scim2_roles_create_role' => 'Create Role',
            'scim2_roles_view_role' => 'View Role',
            
            // Application-specific permissions
            'view_trading_data' => 'View Trading Data',
            'export_trading_data' => 'Export Trading Data',
            'view_limited_trading_data' => 'View Limited Trading Data',
            'manage_security_policies' => 'Manage Security Policies',
            'view_audit_logs' => 'View Audit Logs',
            'manage_sessions' => 'Manage User Sessions',
            'view_own_profile' => 'View Own Profile',
            'edit_own_profile' => 'Edit Own Profile',
            'request_role_change' => 'Request Role Change',
            'change_user_roles' => 'Change User Roles',
        ];
    }

    /**
     * Get WSO2 role mappings for local roles
     */
    public static function getWSO2RoleMappings()
    {
        return [
            'admin' => [
                'wso2_role_name' => 'Admin',
                'permissions' => [
                    'scim2_users_create_user',
                    'scim2_users_view_user', 
                    'scim2_users_delete_user',
                    'scim2_users_update_user',
                    'scim2_users_list_users',
                    'scim2_roles_update_role',
                    'scim2_roles_delete_role',
                    'scim2_roles_create_role',
                    'scim2_roles_view_role',
                    'view_trading_data',
                    'export_trading_data',
                    'manage_security_policies',
                    'view_audit_logs',
                    'manage_sessions'
                ]
            ],
            'hr' => [
                'wso2_role_name' => 'Human Resources',
                'permissions' => [
                    'scim2_users_create_user',
                    'scim2_users_view_user',
                    'scim2_users_delete_user', 
                    'scim2_users_update_user',
                    'scim2_users_list_users',
                    'scim2_roles_update_role',
                    'scim2_roles_view_role',
                    'view_trading_data',
                    'change_user_roles'
                ]
            ],
            'employee' => [
                'wso2_role_name' => 'Employee',
                'permissions' => [
                    'scim2_users_create_user',
                    'scim2_users_view_user',
                    'scim2_users_delete_user',
                    'scim2_users_update_user', 
                    'scim2_users_list_users',
                    'view_own_profile',
                    'edit_own_profile',
                    'request_role_change',
                    'view_limited_trading_data'
                ]
            ]
        ];
    }
}