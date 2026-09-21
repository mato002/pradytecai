<?php

namespace App\Policies;

use App\Models\Position;
use App\Models\User;

class PositionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('careers.view') || $user->can('careers.manage');
    }

    public function view(User $user, Position $position): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $user->can('careers.manage');
    }

    public function update(User $user, Position $position): bool
    {
        return $user->can('careers.manage');
    }

    public function delete(User $user, Position $position): bool
    {
        return $user->can('careers.manage');
    }
}
