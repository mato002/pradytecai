<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, HasRoles, Notifiable;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_super_admin',
    ];

    /**
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_super_admin' => 'boolean',
        ];
    }

    public function accessScopes(): HasMany
    {
        return $this->hasMany(UserAccessScope::class);
    }

    public function isSuperAdmin(): bool
    {
        return (bool) $this->is_super_admin || $this->role === 'super_admin' || $this->hasRole('super_admin');
    }

    /**
     * Legacy helper — maps old admin role / super admin.
     */
    public function isAdmin(): bool
    {
        return $this->isSuperAdmin()
            || $this->role === 'admin'
            || $this->hasAnyRole(['super_admin', 'marketing_admin']);
    }

    public function isHrManager(): bool
    {
        return $this->role === 'hr_manager' || $this->hasRole('hr_manager');
    }

    /**
     * @deprecated Prefer permission:careers.view / careers.manage
     */
    public function canAccessCareers(): bool
    {
        return $this->isSuperAdmin()
            || $this->can('careers.view')
            || $this->can('careers.manage')
            || $this->isHrManager();
    }

    public function hasUnrestrictedScope(): bool
    {
        return $this->isSuperAdmin();
    }

    public function canAccessProduct(?int $productId): bool
    {
        if ($productId === null) {
            return true;
        }

        if ($this->hasUnrestrictedScope()) {
            return true;
        }

        $productScopes = $this->accessScopes
            ->where('scope_type', UserAccessScope::TYPE_PRODUCT);

        // No product scopes assigned = unrestricted for users with the permission
        // (scope is an additional restriction when present).
        if ($productScopes->isEmpty()) {
            return true;
        }

        return $productScopes->contains('scope_id', $productId);
    }

    public function canAccessSocialAccount(?int $socialAccountId): bool
    {
        if ($socialAccountId === null) {
            return true;
        }

        if ($this->hasUnrestrictedScope()) {
            return true;
        }

        $accountScopes = $this->accessScopes
            ->where('scope_type', UserAccessScope::TYPE_SOCIAL_ACCOUNT);

        if ($accountScopes->isEmpty()) {
            return true;
        }

        return $accountScopes->contains('scope_id', $socialAccountId);
    }

    /**
     * @return list<int>|null null means all products
     */
    public function scopedProductIds(): ?array
    {
        if ($this->hasUnrestrictedScope()) {
            return null;
        }

        $ids = $this->accessScopes
            ->where('scope_type', UserAccessScope::TYPE_PRODUCT)
            ->pluck('scope_id')
            ->map(fn ($id) => (int) $id)
            ->values()
            ->all();

        return $ids === [] ? null : $ids;
    }

    public function syncPrimaryRoleSlug(?string $roleSlug): void
    {
        $this->role = $roleSlug ?? 'marketing_analyst';
        $this->is_super_admin = $roleSlug === 'super_admin';
        $this->save();
    }
}
