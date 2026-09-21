<?php

namespace App\Services\Social;

use App\Models\ContentDestination;
use Illuminate\Support\Str;

class BufferPublisher implements SocialPublisher
{
    public function publish(ContentDestination $destination): array
    {
        $destination->loadMissing(['socialAccount.integration', 'contentItem']);

        $account = $destination->socialAccount;
        $integration = $account?->integration;

        $connected = $integration
            && $integration->status === 'connected'
            && (filled($integration->external_account_name) || filled($integration->getRawOriginal('access_token')));

        if (! $connected) {
            $destination->update([
                'status' => 'failed',
                'failed_at' => now(),
                'failure_message' => 'No connected integration for this social account.',
            ]);

            return [
                'success' => false,
                'external_post_id' => null,
                'message' => 'No connected integration for this social account.',
            ];
        }

        $externalId = 'buf_'.Str::lower(Str::random(16));

        $destination->update([
            'status' => 'published',
            'external_post_id' => $externalId,
            'published_at' => now(),
            'failed_at' => null,
            'failure_message' => null,
        ]);

        if ($account) {
            $account->update(['last_posted_at' => now()]);
        }

        return [
            'success' => true,
            'external_post_id' => $externalId,
            'message' => null,
        ];
    }
}
