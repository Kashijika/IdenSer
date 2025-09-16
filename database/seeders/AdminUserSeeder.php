<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Note: This seeder is deprecated as users are now managed via WSO2 Identity Server.
     * Users are no longer stored in the local database.
     * 
     * To set up admin users, please configure them in WSO2 Identity Server
     * and assign the appropriate roles (Admin, Human Resources, Employee).
     */
    public function run(): void
    {
        $this->command->info('Users are now managed via WSO2 Identity Server.');
        $this->command->info('Please configure users directly in WSO2 IS with appropriate roles:');
        $this->command->info('- Admin: Full system access');
        $this->command->info('- Human Resources: User and limited role management');
        $this->command->info('- Employee: Basic access with own profile management');
    }
}
