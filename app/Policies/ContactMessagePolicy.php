<?php

namespace App\Policies;

use App\Models\ContactMessage;
use App\Models\User;

class ContactMessagePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('leads.view');
    }

    public function view(User $user, ContactMessage $message): bool
    {
        return $user->can('leads.view') && $user->canAccessProduct($message->product_id);
    }

    public function update(User $user, ContactMessage $message): bool
    {
        return $user->can('leads.manage') && $user->canAccessProduct($message->product_id);
    }

    public function delete(User $user, ContactMessage $message): bool
    {
        return $user->can('leads.manage') && $user->canAccessProduct($message->product_id);
    }

    public function assign(User $user, ContactMessage $message): bool
    {
        return $user->can('leads.assign') && $user->canAccessProduct($message->product_id);
    }
}
