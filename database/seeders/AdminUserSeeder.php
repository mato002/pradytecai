<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@pradytecai.com'],
            [
                'name' => 'Admin User',
                'password' => 'admin123',
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'hr@pradytecai.com'],
            [
                'name' => 'HR Manager',
                'password' => 'hr123',
                'role' => 'hr_manager',
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('Default login accounts ready:');
        $this->command->info('  Admin → admin@pradytecai.com / admin123');
        $this->command->info('  HR    → hr@pradytecai.com / hr123');
        $this->command->warn('Change these passwords after first login.');
    }
}


