<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('security_policies', function (Blueprint $table) {
            $table->id();
            $table->string('policy_name')->unique();
            $table->string('display_name');
            $table->text('description')->nullable();
            $table->json('settings');
            $table->boolean('is_enabled')->default(true);
            $table->timestamps();
        });

        // Insert default security policies
        DB::table('security_policies')->insert([
            [
                'policy_name' => 'password_policy',
                'display_name' => 'Password Policy',
                'description' => 'Password complexity and security requirements',
                'settings' => json_encode([
                    'min_length' => 8,
                    'require_uppercase' => true,
                    'require_lowercase' => true,
                    'require_numbers' => true,
                    'require_special_chars' => true,
                    'password_expiry_days' => 90
                ]),
                'is_enabled' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'policy_name' => 'session_policy',
                'display_name' => 'Session Management',
                'description' => 'User session timeout and security settings',
                'settings' => json_encode([
                    'session_timeout_minutes' => 30,
                    'max_concurrent_sessions' => 3,
                    'remember_me_enabled' => true,
                    'auto_logout_enabled' => true
                ]),
                'is_enabled' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'policy_name' => 'mfa_policy',
                'display_name' => 'Multi-Factor Authentication',
                'description' => 'Two-factor authentication requirements',
                'settings' => json_encode([
                    'mfa_required_for_admins' => true,
                    'mfa_required_for_hr' => false,
                    'mfa_required_for_employees' => false,
                    'backup_codes_enabled' => true
                ]),
                'is_enabled' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('security_policies');
    }
};