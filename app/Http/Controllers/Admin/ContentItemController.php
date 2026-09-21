<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\PublishContentJob;
use App\Models\Campaign;
use App\Models\ContentDestination;
use App\Models\ContentItem;
use App\Models\Product;
use App\Models\SiteSetting;
use App\Models\SocialAccount;
use App\Services\ActivityLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContentItemController extends Controller
{
    public function index(Request $request): View
    {
        return $this->library($request);
    }

    public function library(Request $request): View
    {
        $this->authorize('viewAny', ContentItem::class);

        $query = ContentItem::query()
            ->with(['product', 'campaign', 'author', 'destinations.socialAccount'])
            ->visibleTo($request->user())
            ->latest();

        if ($productId = session('marketing.product_id')) {
            $query->where('product_id', $productId);
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('caption', 'like', "%{$search}%");
            });
        }

        $items = $query->paginate(20)->withQueryString();
        $approvalRequired = SiteSetting::get('content.approval_required', '1') === '1';

        return view('admin.content.library', compact('items', 'approvalRequired'));
    }

    public function create(): View
    {
        $this->authorize('create', ContentItem::class);

        return view('admin.content.create', $this->formData());
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', ContentItem::class);

        $data = $this->validated($request);
        $socialAccountIds = $data['social_account_ids'] ?? [];
        unset($data['social_account_ids']);

        $data['author_id'] = $request->user()->id;
        $data['status'] = $data['status'] ?? 'draft';

        $item = ContentItem::create($data);
        $this->syncDestinations($item, $socialAccountIds);

        ActivityLogService::created($item, "Created content: {$item->title}");

        return redirect()
            ->route('admin.content.edit', $item)
            ->with('success', 'Content created.');
    }

    public function edit(ContentItem $content): View
    {
        $this->authorize('update', $content);

        $content->load('destinations');

        return view('admin.content.edit', array_merge($this->formData(), [
            'item' => $content,
            'selectedAccountIds' => $content->destinations->pluck('social_account_id')->all(),
            'approvalRequired' => SiteSetting::get('content.approval_required', '1') === '1',
        ]));
    }

    public function update(Request $request, ContentItem $content): RedirectResponse
    {
        $this->authorize('update', $content);

        $data = $this->validated($request);
        $socialAccountIds = $data['social_account_ids'] ?? [];
        unset($data['social_account_ids']);

        $old = $content->getAttributes();
        $content->update($data);
        $this->syncDestinations($content, $socialAccountIds);

        ActivityLogService::updated($content, $old, "Updated content: {$content->title}");

        return redirect()
            ->route('admin.content.edit', $content)
            ->with('success', 'Content updated.');
    }

    public function destroy(ContentItem $content): RedirectResponse
    {
        $this->authorize('delete', $content);

        ActivityLogService::deleted($content, "Deleted content: {$content->title}");
        $content->delete();

        return redirect()
            ->route('admin.content.library')
            ->with('success', 'Content deleted.');
    }

    public function submitForReview(ContentItem $content): RedirectResponse
    {
        $this->authorize('update', $content);

        $content->update(['status' => 'in_review']);
        ActivityLogService::custom('submitted', $content, "Submitted for review: {$content->title}");

        return back()->with('success', 'Submitted for review.');
    }

    public function approve(ContentItem $content): RedirectResponse
    {
        $this->authorize('approve', $content);

        $content->update([
            'status' => 'approved',
            'approver_id' => auth()->id(),
        ]);

        ActivityLogService::custom('approved', $content, "Approved content: {$content->title}");

        return back()->with('success', 'Content approved.');
    }

    public function schedule(Request $request, ContentItem $content): RedirectResponse
    {
        $this->authorize('publish', $content);
        $this->assertCanScheduleOrPublish($content);

        $data = $request->validate([
            'scheduled_at' => ['required', 'date', 'after:now'],
        ]);

        $content->update([
            'status' => 'scheduled',
            'scheduled_at' => $data['scheduled_at'],
        ]);

        $content->destinations()->whereIn('status', ['pending', 'failed'])->update([
            'status' => 'scheduled',
        ]);

        ActivityLogService::custom('scheduled', $content, "Scheduled content: {$content->title}");

        return back()->with('success', 'Content scheduled.');
    }

    public function publish(ContentItem $content): RedirectResponse
    {
        $this->authorize('publish', $content);
        $this->assertCanScheduleOrPublish($content);

        $content->update([
            'status' => 'scheduled',
            'published_at' => $content->published_at ?? now(),
        ]);

        PublishContentJob::dispatch($content->id);

        ActivityLogService::custom('published', $content, "Publish queued: {$content->title}");

        return back()->with('success', 'Publish job queued.');
    }

    public function approvals(Request $request): View
    {
        $this->authorize('viewAny', ContentItem::class);

        $items = ContentItem::query()
            ->with(['product', 'author'])
            ->visibleTo($request->user())
            ->where('status', 'in_review')
            ->when(session('marketing.product_id'), fn ($q, $id) => $q->where('product_id', $id))
            ->latest()
            ->paginate(20);

        return view('admin.content.approvals', compact('items'));
    }

    private function assertCanScheduleOrPublish(ContentItem $content): void
    {
        $approvalRequired = SiteSetting::get('content.approval_required', '1') === '1';

        if ($approvalRequired && ! in_array($content->status, ['approved', 'scheduled'], true)) {
            abort(403, 'Content must be approved before scheduling or publishing.');
        }

        if (! $approvalRequired && ! auth()->user()->can('content.publish')) {
            abort(403);
        }
    }

    private function syncDestinations(ContentItem $item, array $socialAccountIds): void
    {
        $existing = $item->destinations()->pluck('social_account_id')->all();
        $toRemove = array_diff($existing, $socialAccountIds);
        if ($toRemove !== []) {
            $item->destinations()->whereIn('social_account_id', $toRemove)->delete();
        }

        foreach ($socialAccountIds as $accountId) {
            ContentDestination::firstOrCreate(
                [
                    'content_item_id' => $item->id,
                    'social_account_id' => $accountId,
                ],
                ['status' => 'pending']
            );
        }
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'product_id' => ['nullable', 'integer', 'exists:products,id'],
            'campaign_id' => ['nullable', 'integer', 'exists:campaigns,id'],
            'title' => ['required', 'string', 'max:255'],
            'caption' => ['nullable', 'string'],
            'body' => ['nullable', 'string'],
            'cta_label' => ['nullable', 'string', 'max:255'],
            'destination_url' => ['nullable', 'url', 'max:500'],
            'status' => ['nullable', 'string', 'in:draft,in_review,approved,scheduled,published,rejected'],
            'social_account_ids' => ['nullable', 'array'],
            'social_account_ids.*' => ['integer', 'exists:social_accounts,id'],
        ]);
    }

    private function formData(): array
    {
        $user = auth()->user();

        return [
            'products' => Product::query()->visibleTo($user)->ordered()->get(['id', 'name']),
            'campaigns' => Campaign::query()->visibleTo($user)->orderBy('name')->get(['id', 'name']),
            'socialAccounts' => SocialAccount::query()->visibleTo($user)->orderBy('name')->get(['id', 'name', 'platform']),
        ];
    }
}
