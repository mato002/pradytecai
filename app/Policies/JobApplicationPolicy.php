<?php

namespace App\Policies;

use App\Models\JobApplication;
use App\Models\User;

class JobApplicationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('careers.view') || $user->can('careers.manage');
    }

    public function view(User $user, JobApplication $application): bool
    {
        return $this->viewAny($user);
    }

    public function update(User $user, JobApplication $application): bool
    {
        return $user->can('careers.manage');
    }

    public function delete(User $user, JobApplication $application): bool
    {
        return $user->can('careers.manage');
    }
}
