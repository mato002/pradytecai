<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['email' => 'admin@pradytecai.com'],
            [
                'name' => 'Admin User',
                'password' => 'admin123',
                'role' => 'super_admin',
                'is_super_admin' => true,
                'email_verified_at' => now(),
            ]
        );

        $hr = User::updateOrCreate(
            ['email' => 'hr@pradytecai.com'],
            [
                'name' => 'HR Manager',
                'password' => 'hr123',
                'role' => 'hr_manager',
                'is_super_admin' => false,
                'email_verified_at' => now(),
            ]
        );

        // Roles assigned in PermissionSeeder (runs after this if called together;
        // also sync here so standalone seed still works after permissions exist).
        if (class_exists(\Spatie\Permission\Models\Role::class)
            && \Spatie\Permission\Models\Role::where('name', 'super_admin')->exists()) {
            $admin->syncRoles(['super_admin']);
            $hr->syncRoles(['hr_manager']);
        }

        $this->command->info('Default login accounts ready:');
        $this->command->info('  Admin → admin@pradytecai.com / admin123');
        $this->command->info('  HR    → hr@pradytecai.com / hr123');
        $this->command->warn('Change these passwords after first login.');
    }
}
