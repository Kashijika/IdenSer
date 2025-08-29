<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'first_name',
        'last_name',
        'username',
        'email',
        'password',
        'google_id',
        'avatar',
        'mobile',
        'country',
        'role_id',
        'status',
        'last_login_at',
        'last_login_ip',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Get the user's role.
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Get role change requests made by this user.
     */
    public function roleChangeRequests()
    {
        return $this->hasMany(RoleChangeRequest::class);
    }

    /**
     * Get role change requests reviewed by this user.
     */
    public function reviewedRoleChangeRequests()
    {
        return $this->hasMany(RoleChangeRequest::class, 'reviewed_by');
    }

    /**
     * Get audit logs for this user.
     */
    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class);
    }

    /**
     * Check if user has a specific permission.
     */
    public function hasPermission($permission)
    {
        if (!$this->role) {
            return false;
        }

        $permissions = $this->role->permissions ?? [];
        return in_array($permission, $permissions);
    }

    /**
     * Check if user has any of the specified permissions.
     */
    public function hasAnyPermission($permissions)
    {
        foreach ($permissions as $permission) {
            if ($this->hasPermission($permission)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if user is admin.
     */
    public function isAdmin()
    {
        return $this->role && $this->role->name === 'admin';
    }

    /**
     * Check if user is HR.
     */
    public function isHR()
    {
        return $this->role && $this->role->name === 'hr';
    }

    /**
     * Check if user is employee.
     */
    public function isEmployee()
    {
        return $this->role && $this->role->name === 'employee';
    }

    /**
     * Get user's initials for avatar.
     */
    public function getInitialsAttribute()
    {
        $firstInitial = $this->first_name ? substr($this->first_name, 0, 1) : '';
        $lastInitial = $this->last_name ? substr($this->last_name, 0, 1) : '';
        
        if (!$firstInitial && !$lastInitial) {
            return substr($this->name ?? $this->email, 0, 2);
        }
        
        return strtoupper($firstInitial . $lastInitial);
    }

    /**
     * Get user's full name.
     */
    public function getFullNameAttribute()
    {
        if ($this->first_name && $this->last_name) {
            return $this->first_name . ' ' . $this->last_name;
        }
        return $this->name ?? $this->email;
    }

    /**
     * Get profile completion percentage.
     */
    public function getProfileCompletionAttribute()
    {
        $fields = ['first_name', 'last_name', 'email', 'mobile', 'country', 'avatar'];
        $completed = 0;
        
        foreach ($fields as $field) {
            if (!empty($this->{$field})) {
                $completed++;
            }
        }
        
        return round(($completed / count($fields)) * 100);
    }

    /**
     * Scope for active users.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for users with specific role.
     */
    public function scopeWithRole($query, $roleName)
    {
        return $query->whereHas('role', function ($q) use ($roleName) {
            $q->where('name', $roleName);
        });
    }
}