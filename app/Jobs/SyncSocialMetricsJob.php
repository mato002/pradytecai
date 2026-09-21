<?php

namespace App\Jobs;

use App\Models\Integration;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncSocialMetricsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $integrations = Integration::query()
            ->whereIn('provider', ['buffer', 'meta', 'linkedin'])
            ->where('status', 'connected')
            ->get();

        if ($integrations->isEmpty()) {
            Log::info('SyncSocialMetricsJob: no connected social integrations; skipping.');

            return;
        }

        // Credentials present but live API sync is not implemented yet.
        Log::info('SyncSocialMetricsJob: integrations found; metric sync stub completed.', [
            'count' => $integrations->count(),
        ]);
    }
}
