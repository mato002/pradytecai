<?php

namespace App\Services\Social;

use App\Models\ContentDestination;

class NullPublisher implements SocialPublisher
{
    public function publish(ContentDestination $destination): array
    {
        $destination->update([
            'status' => 'failed',
            'failed_at' => now(),
            'failure_message' => 'No social publisher configured.',
        ]);

        return [
            'success' => false,
            'external_post_id' => null,
            'message' => 'No social publisher configured.',
        ];
    }
}
