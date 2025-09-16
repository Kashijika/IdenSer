<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoleChangeRequest extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'wso2_user_id',
        'wso2_user_email',
        'wso2_user_name',
        'requested_role_id',
        'current_role_id',
        'reason',
        'status',
        'wso2_reviewed_by_id',
        'wso2_reviewed_by_email',
        'review_notes',
        'reviewed_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    /**
     * Get the requested role name (WSO2-compatible)
     * Since we're using WSO2 roles, we'll return role name directly
     */
    public function requestedRole()
    {
        // For WSO2 implementation, we should store role names directly
        // This is a placeholder that maintains compatibility
        return $this->requested_role_name ?? 'Unknown';
    }

    /**
     * Get the current role name (WSO2-compatible)
     * Since we're using WSO2 roles, we'll return role name directly
     */
    public function currentRole()
    {
        // For WSO2 implementation, we should store role names directly
        // This is a placeholder that maintains compatibility
        return $this->current_role_name ?? 'Unknown';
    }

    /**
     * Get formatted user display name
     */
    public function getUserDisplayNameAttribute()
    {
        return $this->wso2_user_name ?: $this->wso2_user_email;
    }

    /**
     * Get formatted reviewer display name
     */
    public function getReviewerDisplayNameAttribute()
    {
        return $this->wso2_reviewed_by_email;
    }

    /**
     * Scope for pending requests.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for approved requests.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope for rejected requests.
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }
}