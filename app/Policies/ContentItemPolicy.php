<?php

namespace App\Policies;

use App\Models\ContentItem;
use App\Models\User;

class ContentItemPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('content.view');
    }

    public function view(User $user, ContentItem $content): bool
    {
        return $user->can('content.view') && $user->canAccessProduct($content->product_id);
    }

    public function create(User $user): bool
    {
        return $user->can('content.create');
    }

    public function update(User $user, ContentItem $content): bool
    {
        return $user->can('content.edit') && $user->canAccessProduct($content->product_id);
    }

    public function delete(User $user, ContentItem $content): bool
    {
        return $user->can('content.delete') && $user->canAccessProduct($content->product_id);
    }

    public function approve(User $user, ContentItem $content): bool
    {
        return $user->can('content.approve') && $user->canAccessProduct($content->product_id);
    }

    public function publish(User $user, ContentItem $content): bool
    {
        return $user->can('content.publish') && $user->canAccessProduct($content->product_id);
    }
}
