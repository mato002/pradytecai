<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\ContentItem;
use App\Models\MetricSnapshot;
use App\Models\Product;
use App\Models\SocialAccount;
use App\Models\WebsiteEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AnalyticsController extends Controller
{
    public function overview(Request $request): View
    {
        $this->guard();

        $aggregates = $this->aggregateMetrics();
        $eventCounts = WebsiteEvent::query()
            ->when(session('marketing.product_id'), fn ($q) => $q->where('product_id', session('marketing.product_id')))
            ->where('occurred_at', '>=', now()->subDays(30))
            ->select('event_name', DB::raw('count(*) as total'))
            ->groupBy('event_name')
            ->pluck('total', 'event_name');

        return view('admin.analytics.overview', [
            'aggregates' => $aggregates,
            'eventCounts' => $eventCounts,
            'hasData' => $aggregates->isNotEmpty() || $eventCounts->isNotEmpty(),
        ]);
    }

    public function social(Request $request): View
    {
        $this->guard();

        $accounts = SocialAccount::query()
            ->visibleTo($request->user())
            ->when(session('marketing.product_id'), fn ($q) => $q->where('product_id', session('marketing.product_id')))
            ->with('metricSnapshots')
            ->orderBy('name')
            ->get();

        $aggregates = MetricSnapshot::query()
            ->where('measurable_type', SocialAccount::class)
            ->whereIn('measurable_id', $accounts->pluck('id'))
            ->select('metric_key', DB::raw('SUM(value) as total'))
            ->groupBy('metric_key')
            ->pluck('total', 'metric_key');

        return view('admin.analytics.social', [
            'accounts' => $accounts,
            'aggregates' => $aggregates,
            'hasData' => $aggregates->isNotEmpty(),
        ]);
    }

    public function website(Request $request): View
    {
        $this->guard();

        $events = WebsiteEvent::query()
            ->when(session('marketing.product_id'), fn ($q) => $q->where('product_id', session('marketing.product_id')))
            ->where('occurred_at', '>=', now()->subDays(30))
            ->select('event_name', DB::raw('count(*) as total'))
            ->groupBy('event_name')
            ->orderByDesc('total')
            ->get();

        return view('admin.analytics.website', [
            'events' => $events,
            'hasData' => $events->isNotEmpty(),
        ]);
    }

    public function campaigns(Request $request): View
    {
        $this->guard();

        $campaigns = Campaign::query()
            ->visibleTo($request->user())
            ->when(session('marketing.product_id'), function ($q) {
                $q->whereHas('products', fn ($p) => $p->where('products.id', session('marketing.product_id')));
            })
            ->withCount('contentItems')
            ->orderBy('name')
            ->get();

        $aggregates = MetricSnapshot::query()
            ->where('measurable_type', Campaign::class)
            ->whereIn('measurable_id', $campaigns->pluck('id'))
            ->select('metric_key', DB::raw('SUM(value) as total'))
            ->groupBy('metric_key')
            ->pluck('total', 'metric_key');

        return view('admin.analytics.campaigns', [
            'campaigns' => $campaigns,
            'aggregates' => $aggregates,
            'hasData' => $aggregates->isNotEmpty() || $campaigns->isNotEmpty(),
        ]);
    }

    public function products(Request $request): View
    {
        $this->guard();

        $products = Product::query()
            ->visibleTo($request->user())
            ->when(session('marketing.product_id'), fn ($q) => $q->where('id', session('marketing.product_id')))
            ->withCount(['contentItems', 'contactMessages', 'demoRequests'])
            ->ordered()
            ->get();

        return view('admin.analytics.products', [
            'products' => $products,
            'hasData' => $products->isNotEmpty(),
        ]);
    }

    public function content(Request $request): View
    {
        $this->guard();

        $items = ContentItem::query()
            ->visibleTo($request->user())
            ->when(session('marketing.product_id'), fn ($q) => $q->where('product_id', session('marketing.product_id')))
            ->withCount('destinations')
            ->whereIn('status', ['published', 'scheduled'])
            ->latest('published_at')
            ->limit(50)
            ->get();

        return view('admin.analytics.content', [
            'items' => $items,
            'hasData' => $items->isNotEmpty(),
        ]);
    }

    private function guard(): void
    {
        abort_unless(auth()->user()->can('analytics.view'), 403);
    }

    private function aggregateMetrics()
    {
        return MetricSnapshot::query()
            ->select('metric_key', DB::raw('SUM(value) as total'), DB::raw('COUNT(*) as samples'))
            ->groupBy('metric_key')
            ->orderBy('metric_key')
            ->get();
    }
}
