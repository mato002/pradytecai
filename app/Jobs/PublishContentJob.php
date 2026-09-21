<?php

namespace App\Jobs;

use App\Models\ContentItem;
use App\Services\Social\SocialPublisher;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PublishContentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public int $contentItemId)
    {
    }

    public function handle(SocialPublisher $publisher): void
    {
        $item = ContentItem::with('destinations.socialAccount.integration')->find($this->contentItemId);

        if (! $item) {
            return;
        }

        $destinations = $item->destinations()
            ->whereIn('status', ['pending', 'scheduled', 'failed'])
            ->get();

        $anySuccess = false;

        foreach ($destinations as $destination) {
            $result = $publisher->publish($destination);
            if ($result['success'] ?? false) {
                $anySuccess = true;
            }
        }

        $item->update([
            'status' => $anySuccess ? 'published' : 'failed',
            'published_at' => $anySuccess ? ($item->published_at ?? now()) : $item->published_at,
        ]);

        if ($anySuccess && $item->product) {
            $item->product->update(['last_marketed_at' => now()]);
        }
    }
}
