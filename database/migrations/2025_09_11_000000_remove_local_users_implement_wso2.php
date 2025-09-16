<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop foreign key constraints first
        Schema::table('role_change_requests', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['reviewed_by']);
        });

        Schema::table('audit_logs', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        // Drop the users table completely
        Schema::dropIfExists('users');

        // Update audit_logs to store WSO2 user information instead
        Schema::table('audit_logs', function (Blueprint $table) {
            $table->string('wso2_user_id')->nullable()->after('id');
            $table->string('wso2_user_email')->nullable()->after('wso2_user_id');
            $table->string('wso2_user_name')->nullable()->after('wso2_user_email');
        });

        // Update role_change_requests to work with WSO2 users
        Schema::table('role_change_requests', function (Blueprint $table) {
            $table->string('wso2_user_id')->nullable()->after('id');
            $table->string('wso2_user_email')->nullable()->after('wso2_user_id');
            $table->string('wso2_user_name')->nullable()->after('wso2_user_email');
            $table->string('wso2_reviewed_by_id')->nullable()->after('reviewed_at');
            $table->string('wso2_reviewed_by_email')->nullable()->after('wso2_reviewed_by_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate users table
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('username')->nullable()->unique();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->string('google_id')->nullable()->unique();
            $table->string('avatar')->nullable();
            $table->string('mobile')->nullable();
            $table->string('country')->nullable();
            $table->unsignedBigInteger('role_id')->nullable();
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');
            $table->timestamp('last_login_at')->nullable();
            $table->string('last_login_ip')->nullable();
            $table->rememberToken();
            $table->timestamps();

            $table->foreign('role_id')->references('id')->on('roles')->onDelete('set null');
        });

        // Remove WSO2 columns from audit_logs
        Schema::table('audit_logs', function (Blueprint $table) {
            $table->dropColumn(['wso2_user_id', 'wso2_user_email', 'wso2_user_name']);
            $table->unsignedBigInteger('user_id')->nullable()->after('id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });

        // Remove WSO2 columns from role_change_requests
        Schema::table('role_change_requests', function (Blueprint $table) {
            $table->dropColumn(['wso2_user_id', 'wso2_user_email', 'wso2_user_name', 'wso2_reviewed_by_id', 'wso2_reviewed_by_email']);
            $table->unsignedBigInteger('user_id')->nullable()->after('id');
            $table->unsignedBigInteger('reviewed_by')->nullable()->after('reviewed_at');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('reviewed_by')->references('id')->on('users')->onDelete('set null');
        });
    }
};
