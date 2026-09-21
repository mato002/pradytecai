<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\ContactMessage;
use App\Models\ContentDestination;
use App\Models\ContentItem;
use App\Models\MarketingAlert;
use App\Models\MarketingTask;
use App\Models\MetricSnapshot;
use App\Models\Product;
use App\Models\SocialAccount;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if (! $user->can('dashboard.view')) {
            return redirect()->route('admin.positions.index');
        }

        $productId = session('marketing.product_id');

        $alertBase = MarketingAlert::query()->open();
        $this->applyProductId($alertBase, $productId);
        $alertsBySeverity = (clone $alertBase)
            ->select('severity', DB::raw('count(*) as total'))
            ->groupBy('severity')
            ->pluck('total', 'severity');

        $campaignsRunning = Campaign::query()
            ->visibleTo($user)
            ->active();
        $this->applyCampaignProductFilter($campaignsRunning, $productId);

        $contentScheduled = ContentItem::query()
            ->visibleTo($user)
            ->whereNotNull('scheduled_at')
            ->whereBetween('scheduled_at', [now(), now()->addDays(7)]);
        $this->applyProductId($contentScheduled, $productId);

        $postsPublished = ContentDestination::query()
            ->where('status', 'published')
            ->where('published_at', '>=', now()->subDays(7))
            ->whereHas('contentItem', function ($q) use ($user, $productId) {
                $q->visibleTo($user);
                $this->applyProductId($q, $productId);
            });

        $overdueTasks = MarketingTask::query()->overdue();
        $this->applyProductId($overdueTasks, $productId);

        $leadsAwaiting = ContactMessage::query()
            ->visibleTo($user)
            ->awaitingFollowUp();
        $this->applyProductId($leadsAwaiting, $productId);

        $failedDestinations = ContentDestination::query()
            ->where('status', 'failed')
            ->whereHas('contentItem', function ($q) use ($user, $productId) {
                $q->visibleTo($user);
                $this->applyProductId($q, $productId);
            });

        $inactiveProducts = Product::query()
            ->visibleTo($user)
            ->active()
            ->where(function ($q) {
                $q->whereNull('last_marketed_at')
                    ->orWhere('last_marketed_at', '<', now()->subDays(14))
                    ->orWhereDoesntHave('contentItems');
            });
        if ($productId) {
            $inactiveProducts->where('id', $productId);
        }

        $inactiveSocial = SocialAccount::query()
            ->visibleTo($user)
            ->active()
            ->where(function ($q) {
                $q->whereNull('last_posted_at')
                    ->orWhere('last_posted_at', '<', now()->subDays(5));
            });
        $this->applyProductId($inactiveSocial, $productId);

        $leadsLast7d = ContactMessage::query()
            ->visibleTo($user)
            ->where('created_at', '>=', now()->subDays(7));
        $this->applyProductId($leadsLast7d, $productId);

        $demosLast7d = ContactMessage::query()
            ->visibleTo($user)
            ->where('request_type', 'demo')
            ->where('created_at', '>=', now()->subDays(7));
        $this->applyProductId($demosLast7d, $productId);

        $qualifiedLeads = ContactMessage::query()
            ->visibleTo($user)
            ->where('status', 'qualified');
        $this->applyProductId($qualifiedLeads, $productId);

        $responseHoursQuery = ContactMessage::query()
            ->visibleTo($user)
            ->whereNotNull('first_responded_at')
            ->whereNotNull('created_at');
        $this->applyProductId($responseHoursQuery, $productId);
        $responded = (clone $responseHoursQuery)
            ->whereNotNull('first_responded_at')
            ->get(['created_at', 'first_responded_at']);

        $avgFirstResponseHours = $responded->isEmpty()
            ? null
            : round($responded->avg(fn ($lead) => $lead->created_at->diffInHours($lead->first_responded_at)), 1);

        $metricSums = MetricSnapshot::query()
            ->whereIn('metric_key', ['reach', 'impressions', 'engagement'])
            ->where('captured_at', '>=', now()->subDays(30))
            ->select('metric_key', DB::raw('SUM(value) as total'))
            ->groupBy('metric_key')
            ->pluck('total', 'metric_key');

        $criticalAlerts = MarketingAlert::query()
            ->open()
            ->where('severity', 'critical')
            ->when($productId, fn ($q) => $q->where('product_id', $productId))
            ->latest('triggered_at')
            ->limit(8)
            ->get();

        $stats = [
            'alerts_critical' => (int) ($alertsBySeverity['critical'] ?? 0),
            'alerts_warning' => (int) ($alertsBySeverity['warning'] ?? 0),
            'campaigns_running' => $campaignsRunning->count(),
            'content_scheduled_7d' => $contentScheduled->count(),
            'posts_published_7d' => $postsPublished->count(),
            'overdue_tasks' => $overdueTasks->count(),
            'leads_awaiting_follow_up' => $leadsAwaiting->count(),
            'failed_destinations' => $failedDestinations->count(),
            'inactive_products' => $inactiveProducts->count(),
            'inactive_social_accounts' => $inactiveSocial->count(),
            'leads_last_7d' => $leadsLast7d->count(),
            'demos_last_7d' => $demosLast7d->count(),
            'qualified_leads' => $qualifiedLeads->count(),
            'avg_first_response_hours' => $avgFirstResponseHours !== null
                ? round((float) $avgFirstResponseHours, 1)
                : null,
            'metric_reach' => isset($metricSums['reach']) ? (float) $metricSums['reach'] : null,
            'metric_impressions' => isset($metricSums['impressions']) ? (float) $metricSums['impressions'] : null,
            'metric_engagement' => isset($metricSums['engagement']) ? (float) $metricSums['engagement'] : null,
        ];

        return view('admin.dashboard', compact('stats', 'criticalAlerts'));
    }

    private function applyProductId($query, $productId, string $column = 'product_id'): void
    {
        if ($productId) {
            $query->where($column, $productId);
        }
    }

    private function applyCampaignProductFilter($query, $productId): void
    {
        if ($productId) {
            $query->whereHas('products', fn ($q) => $q->where('products.id', $productId));
        }
    }
}
