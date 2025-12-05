<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create default admin user
        User::firstOrCreate(
            ['email' => 'admin@pradytecai.com'],
            [
                'name' => 'Admin User',
                'email' => 'admin@pradytecai.com',
                'password' => Hash::make('admin123'),
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('Admin user created successfully!');
        $this->command->info('Email: admin@pradytecai.com');
        $this->command->info('Password: admin123');
        $this->command->warn('⚠️  Please change the password after first login!');
    }
}


