<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SecurityPolicy extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'policy_name',
        'display_name',
        'description',
        'settings',
        'is_enabled',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'settings' => 'array',
        'is_enabled' => 'boolean',
    ];

    /**
     * Get policy setting value.
     */
    public function getSetting($key, $default = null)
    {
        return $this->settings[$key] ?? $default;
    }

    /**
     * Set policy setting value.
     */
    public function setSetting($key, $value)
    {
        $settings = $this->settings ?? [];
        $settings[$key] = $value;
        $this->settings = $settings;
        $this->save();
    }

    /**
     * Get password policy settings.
     */
    public static function getPasswordPolicy()
    {
        return self::where('policy_name', 'password_policy')->first();
    }

    /**
     * Get session policy settings.
     */
    public static function getSessionPolicy()
    {
        return self::where('policy_name', 'session_policy')->first();
    }

    /**
     * Get MFA policy settings.
     */
    public static function getMFAPolicy()
    {
        return self::where('policy_name', 'mfa_policy')->first();
    }

    /**
     * Scope for enabled policies.
     */
    public function scopeEnabled($query)
    {
        return $query->where('is_enabled', true);
    }
}