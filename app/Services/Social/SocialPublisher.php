<?php

namespace App\Services\Social;

use App\Models\ContentDestination;

interface SocialPublisher
{
    /**
     * Publish a content destination to the connected social platform.
     *
     * @return array{success: bool, external_post_id?: string|null, message?: string|null}
     */
    public function publish(ContentDestination $destination): array;
}
