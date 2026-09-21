<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $guard = 'web';
        $permissions = config('marketing_permissions.permissions', []);
        $roles = config('marketing_permissions.roles', []);

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, $guard);
        }

        foreach ($roles as $slug => $definition) {
            $role = Role::findOrCreate($slug, $guard);

            $assigned = $definition['permissions'] ?? [];
            if ($assigned === '*') {
                $role->syncPermissions(Permission::where('guard_name', $guard)->get());
            } else {
                $role->syncPermissions($assigned);
            }
        }

        // Map existing users by legacy/primary role column.
        User::query()->each(function (User $user) {
            $slug = match ($user->role) {
                'admin', 'super_admin' => 'super_admin',
                'hr_manager' => 'hr_manager',
                default => in_array($user->role, array_keys(config('marketing_permissions.roles')), true)
                    ? $user->role
                    : null,
            };

            if ($slug === null) {
                return;
            }

            $user->syncRoles([$slug]);
            $user->is_super_admin = $slug === 'super_admin';
            $user->role = $slug;
            $user->save();
        });
    }
}
