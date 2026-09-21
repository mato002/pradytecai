<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\ContentItem;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContentCalendarController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()->can('content.view'), 403);

        $viewMode = $request->query('view', 'month');
        if (! in_array($viewMode, ['month', 'week', 'list'], true)) {
            $viewMode = 'month';
        }

        $anchor = $request->filled('date')
            ? Carbon::parse($request->query('date'))
            : now();

        if ($viewMode === 'week') {
            $rangeStart = $anchor->copy()->startOfWeek();
            $rangeEnd = $anchor->copy()->endOfWeek();
        } elseif ($viewMode === 'list') {
            $rangeStart = $anchor->copy()->startOfMonth();
            $rangeEnd = $anchor->copy()->addMonths(2)->endOfMonth();
        } else {
            $rangeStart = $anchor->copy()->startOfMonth()->startOfWeek();
            $rangeEnd = $anchor->copy()->endOfMonth()->endOfWeek();
        }

        $user = $request->user();
        $query = ContentItem::query()
            ->with(['product', 'campaign', 'author', 'destinations.socialAccount'])
            ->visibleTo($user)
            ->where(function ($q) use ($rangeStart, $rangeEnd) {
                $q->whereBetween('scheduled_at', [$rangeStart, $rangeEnd])
                    ->orWhereBetween('published_at', [$rangeStart, $rangeEnd]);
            });

        if ($productId = session('marketing.product_id') ?: $request->query('product_id')) {
            $query->where('product_id', $productId);
        }

        if ($request->filled('campaign_id')) {
            $query->where('campaign_id', $request->campaign_id);
        }

        if ($request->filled('channel')) {
            $channel = $request->channel;
            $query->whereHas('destinations.socialAccount', fn ($q) => $q->where('platform', $channel));
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('owner_id')) {
            $query->where('author_id', $request->owner_id);
        }

        $items = $query->orderBy('scheduled_at')->orderBy('published_at')->get();

        $filters = [
            'products' => Product::query()->visibleTo($user)->ordered()->get(['id', 'name']),
            'campaigns' => Campaign::query()->visibleTo($user)->orderBy('name')->get(['id', 'name']),
            'owners' => User::orderBy('name')->get(['id', 'name']),
            'channels' => ['facebook', 'instagram', 'linkedin', 'twitter', 'tiktok', 'youtube'],
        ];

        return view('admin.content.calendar', [
            'items' => $items,
            'viewMode' => $viewMode,
            'anchor' => $anchor,
            'rangeStart' => $rangeStart,
            'rangeEnd' => $rangeEnd,
            'filters' => $filters,
        ]);
    }
}
