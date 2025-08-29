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
     * Get users with this role.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get role change requests for this role.
     */
    public function roleChangeRequests()
    {
        return $this->hasMany(RoleChangeRequest::class, 'requested_role_id');
    }

    /**
     * Get count of users with this role.
     */
    public function getUserCountAttribute()
    {
        return $this->users()->count();
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
     * Get available permissions list.
     */
    public static function getAvailablePermissions()
    {
        return [
            'manage_users' => 'Manage Users',
            'manage_roles' => 'Manage Roles',
            'change_user_roles' => 'Change User Roles',
            'view_trading_data' => 'View Trading Data',
            'export_trading_data' => 'Export Trading Data',
            'view_limited_trading_data' => 'View Limited Trading Data',
            'manage_security_policies' => 'Manage Security Policies',
            'view_audit_logs' => 'View Audit Logs',
            'manage_sessions' => 'Manage User Sessions',
            'view_own_profile' => 'View Own Profile',
            'edit_own_profile' => 'Edit Own Profile',
            'request_role_change' => 'Request Role Change',
        ];
    }
}