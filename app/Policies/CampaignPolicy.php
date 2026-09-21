<?php

namespace App\Policies;

use App\Models\Campaign;
use App\Models\User;

class CampaignPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('campaigns.view');
    }

    public function view(User $user, Campaign $campaign): bool
    {
        if (! $user->can('campaigns.view')) {
            return false;
        }

        $scoped = $user->scopedProductIds();
        if ($scoped === null) {
            return true;
        }

        return $campaign->products()->whereIn('products.id', $scoped)->exists();
    }

    public function create(User $user): bool
    {
        return $user->can('campaigns.create') || $user->can('campaigns.manage');
    }

    public function update(User $user, Campaign $campaign): bool
    {
        return ($user->can('campaigns.edit') || $user->can('campaigns.manage')) && $this->view($user, $campaign);
    }

    public function delete(User $user, Campaign $campaign): bool
    {
        return $user->can('campaigns.manage') && $this->view($user, $campaign);
    }
}
