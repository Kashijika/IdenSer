<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;
use App\Models\AuditLog;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the admin role
        $adminRole = Role::where('name', 'admin')->first();
        
        if (!$adminRole) {
            $this->command->error('Admin role not found. Please run the roles migration first.');
            return;
        }

        // Create the admin user
        $adminUser = User::updateOrCreate([
            'email' => 'admin@admin.com'
        ], [
            'name' => 'System Administrator',
            'first_name' => 'System',
            'last_name' => 'Administrator',
            'username' => 'admin',
            'password' => Hash::make('admin'),
            'email_verified_at' => now(),
            'role_id' => $adminRole->id,
            'status' => 'active',
            'country' => 'United States',
        ]);

        // Create additional test users for different roles
        $hrRole = Role::where('name', 'hr')->first();
        $employeeRole = Role::where('name', 'employee')->first();

        if ($hrRole) {
            User::updateOrCreate([
                'email' => 'hr@swa-media.com'
            ], [
                'name' => 'HR Manager',
                'first_name' => 'Sarah',
                'last_name' => 'Johnson',
                'username' => 'hr.manager',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
                'role_id' => $hrRole->id,
                'status' => 'active',
                'country' => 'United States',
            ]);
        }

        if ($employeeRole) {
            User::updateOrCreate([
                'email' => 'employee@swa-media.com'
            ], [
                'name' => 'John Doe',
                'first_name' => 'John',
                'last_name' => 'Doe',
                'username' => 'john.doe',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
                'role_id' => $employeeRole->id,
                'status' => 'active',
                'country' => 'United States',
            ]);
        }

        // Log the seeder action
        AuditLog::create([
            'user_id' => null,
            'action' => 'database_seed',
            'description' => 'Default users seeded: Admin, HR Manager, Employee',
            'entity_type' => 'User',
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Database Seeder',
            'status' => 'success'
        ]);

        $this->command->info('✅ Default users created successfully:');
        $this->command->line('   🔧 Admin: admin@swa-media.com / password123');
        $this->command->line('   👥 HR: hr@swa-media.com / password123');
        $this->command->line('   👤 Employee: employee@swa-media.com / password123');
    }
}