<?php

namespace App\Jobs;

use App\Models\Integration;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncGa4Job implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $integration = Integration::query()
            ->where('provider', 'ga4')
            ->where('status', 'connected')
            ->first();

        $hasEnv = filled(config('services.google.analytics_property_id') ?? env('GA4_PROPERTY_ID'));

        if (! $integration && ! $hasEnv) {
            Log::info('SyncGa4Job: no GA4 credentials; skipping.');

            return;
        }

        Log::info('SyncGa4Job: credentials present; GA4 sync stub completed.');
    }
}
