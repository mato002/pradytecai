<?php

namespace App\Http\Controllers;

use App\Models\TrackedLink;
use App\Models\WebsiteEvent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TrackedLinkController extends Controller
{
    public function show(Request $request, string $code): RedirectResponse
    {
        $link = TrackedLink::query()->where('code', $code)->firstOrFail();

        $link->increment('click_count');

        WebsiteEvent::create([
            'event_name' => 'tracked_link_click',
            'product_id' => $link->product_id,
            'campaign_id' => $link->campaign_id,
            'tracked_link_id' => $link->id,
            'session_id' => $request->session()->getId(),
            'occurred_at' => now(),
            'meta' => [
                'code' => $link->code,
                'referrer' => $request->headers->get('referer'),
                'ip' => $request->ip(),
            ],
        ]);

        return redirect()->away($link->destination_url);
    }
}
