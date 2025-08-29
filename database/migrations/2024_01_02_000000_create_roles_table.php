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
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('display_name');
            $table->text('description')->nullable();
            $table->json('permissions')->nullable();
            $table->boolean('is_system_role')->default(false);
            $table->timestamps();
        });

        // Insert default roles
        DB::table('roles')->insert([
            [
                'name' => 'admin',
                'display_name' => 'Administrator',
                'description' => 'Full system access with all permissions',
                'permissions' => json_encode([
                    'manage_users',
                    'manage_roles',
                    'view_trading_data',
                    'export_trading_data',
                    'manage_security_policies',
                    'view_audit_logs',
                    'manage_sessions'
                ]),
                'is_system_role' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'hr',
                'display_name' => 'Human Resources',
                'description' => 'User management and data access permissions',
                'permissions' => json_encode([
                    'manage_users',
                    'change_user_roles',
                    'view_trading_data',
                    'export_trading_data',
                    'view_audit_logs'
                ]),
                'is_system_role' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'employee',
                'display_name' => 'Employee',
                'description' => 'Basic user with limited data access',
                'permissions' => json_encode([
                    'view_own_profile',
                    'edit_own_profile',
                    'view_limited_trading_data',
                    'request_role_change'
                ]),
                'is_system_role' => true,
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
        Schema::dropIfExists('roles');
    }
};