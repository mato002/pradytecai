<?php

namespace App\Policies;

use App\Models\SocialAccount;
use App\Models\User;

class SocialAccountPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('social_accounts.view');
    }

    public function view(User $user, SocialAccount $account): bool
    {
        return $user->can('social_accounts.view')
            && $user->canAccessProduct($account->product_id)
            && $user->canAccessSocialAccount($account->id);
    }

    public function create(User $user): bool
    {
        return $user->can('social_accounts.manage');
    }

    public function update(User $user, SocialAccount $account): bool
    {
        return $user->can('social_accounts.manage')
            && $user->canAccessProduct($account->product_id)
            && $user->canAccessSocialAccount($account->id);
    }

    public function delete(User $user, SocialAccount $account): bool
    {
        return $this->update($user, $account);
    }
}
