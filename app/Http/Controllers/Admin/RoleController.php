<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ActivityLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index(): View
    {
        $this->authorizePermission('roles.view');

        $roles = Role::with('permissions')->orderBy('name')->get();
        $roleLabels = collect(config('marketing_permissions.roles'))
            ->mapWithKeys(fn ($def, $slug) => [$slug => $def['label'] ?? $slug]);

        return view('admin.roles.index', compact('roles', 'roleLabels'));
    }

    public function edit(Role $role): View
    {
        $this->authorizePermission('roles.manage');

        $permissions = Permission::orderBy('name')->get()->groupBy(function (Permission $p) {
            return explode('.', $p->name)[0] ?? 'other';
        });

        $role->load('permissions');

        return view('admin.roles.edit', compact('role', 'permissions'));
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        $this->authorizePermission('roles.manage');

        if ($role->name === 'super_admin') {
            return back()->with('error', 'Super Admin permissions cannot be changed.');
        }

        $data = $request->validate([
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ]);

        $role->syncPermissions($data['permissions'] ?? []);

        ActivityLogService::custom('updated', null, "Updated role permissions: {$role->name}");

        return redirect()
            ->route('admin.roles.index')
            ->with('success', 'Role permissions updated.');
    }

    private function authorizePermission(string $permission): void
    {
        abort_unless(auth()->user()->can($permission), 403);
    }
}
