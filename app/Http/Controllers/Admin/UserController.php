<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\SocialAccount;
use App\Models\User;
use App\Models\UserAccessScope;
use App\Services\ActivityLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(): View
    {
        abort_unless(auth()->user()->can('users.view'), 403);

        $users = User::with('roles')->latest()->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    public function create(): View
    {
        abort_unless(auth()->user()->can('users.manage'), 403);

        return view('admin.users.create', $this->formData());
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless(auth()->user()->can('users.manage'), 403);

        $roleSlugs = array_keys(config('marketing_permissions.roles'));

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'string', 'in:'.implode(',', $roleSlugs)],
            'product_scopes' => ['nullable', 'array'],
            'product_scopes.*' => ['integer', 'exists:products,id'],
            'social_account_scopes' => ['nullable', 'array'],
            'social_account_scopes.*' => ['integer', 'exists:social_accounts,id'],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'role' => $data['role'],
            'is_super_admin' => $data['role'] === 'super_admin',
        ]);

        $user->syncRoles([$data['role']]);
        $this->syncScopes($user, $data);

        ActivityLogService::created($user, "Created user {$user->email}");

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User created successfully.');
    }

    public function edit(User $user): View
    {
        abort_unless(auth()->user()->can('users.manage'), 403);

        $user->load('accessScopes', 'roles');

        return view('admin.users.edit', array_merge($this->formData(), [
            'user' => $user,
            'selectedProductScopes' => $user->accessScopes->where('scope_type', UserAccessScope::TYPE_PRODUCT)->pluck('scope_id')->all(),
            'selectedAccountScopes' => $user->accessScopes->where('scope_type', UserAccessScope::TYPE_SOCIAL_ACCOUNT)->pluck('scope_id')->all(),
        ]));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        abort_unless(auth()->user()->can('users.manage'), 403);

        $roleSlugs = array_keys(config('marketing_permissions.roles'));

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'string', 'in:'.implode(',', $roleSlugs)],
            'product_scopes' => ['nullable', 'array'],
            'product_scopes.*' => ['integer', 'exists:products,id'],
            'social_account_scopes' => ['nullable', 'array'],
            'social_account_scopes.*' => ['integer', 'exists:social_accounts,id'],
        ]);

        $payload = [
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => $data['role'],
            'is_super_admin' => $data['role'] === 'super_admin',
        ];

        if (! empty($data['password'])) {
            $payload['password'] = $data['password'];
        }

        $user->update($payload);
        $user->syncRoles([$data['role']]);
        $this->syncScopes($user, $data);

        ActivityLogService::updated($user, null, "Updated user {$user->email}");

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User updated successfully.');
    }

    public function destroy(User $user): RedirectResponse
    {
        abort_unless(auth()->user()->can('users.manage'), 403);

        if ($user->id === auth()->id()) {
            return redirect()
                ->route('admin.users.index')
                ->with('error', 'You cannot delete your own account.');
        }

        ActivityLogService::deleted($user, "Deleted user {$user->email}");
        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User deleted successfully.');
    }

    private function formData(): array
    {
        $roles = collect(config('marketing_permissions.roles'))
            ->map(fn ($def, $slug) => ['slug' => $slug, 'label' => $def['label'] ?? $slug])
            ->values();

        return [
            'roles' => $roles,
            'products' => Product::ordered()->get(['id', 'name']),
            'socialAccounts' => class_exists(SocialAccount::class)
                ? SocialAccount::orderBy('name')->get(['id', 'name', 'platform'])
                : collect(),
        ];
    }

    private function syncScopes(User $user, array $data): void
    {
        $user->accessScopes()->delete();

        if ($user->is_super_admin) {
            return;
        }

        foreach ($data['product_scopes'] ?? [] as $productId) {
            $user->accessScopes()->create([
                'scope_type' => UserAccessScope::TYPE_PRODUCT,
                'scope_id' => $productId,
            ]);
        }

        foreach ($data['social_account_scopes'] ?? [] as $accountId) {
            $user->accessScopes()->create([
                'scope_type' => UserAccessScope::TYPE_SOCIAL_ACCOUNT,
                'scope_id' => $accountId,
            ]);
        }
    }
}
