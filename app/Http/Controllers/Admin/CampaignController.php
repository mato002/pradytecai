<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\Product;
use App\Models\User;
use App\Services\ActivityLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CampaignController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Campaign::class);

        $user = $request->user();
        $query = Campaign::query()
            ->with(['owner', 'products'])
            ->visibleTo($user)
            ->latest();

        if ($productId = session('marketing.product_id')) {
            $query->whereHas('products', fn ($q) => $q->where('products.id', $productId));
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('objective', 'like', "%{$search}%")
                    ->orWhere('utm_campaign', 'like', "%{$search}%");
            });
        }

        $campaigns = $query->paginate(20)->withQueryString();

        return view('admin.campaigns.index', compact('campaigns'));
    }

    public function create(): View
    {
        $this->authorize('create', Campaign::class);

        return view('admin.campaigns.create', $this->formData());
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Campaign::class);

        $data = $this->validated($request);
        $productIds = $data['product_ids'] ?? [];
        unset($data['product_ids']);

        $campaign = Campaign::create($data);
        $campaign->products()->sync($productIds);

        ActivityLogService::created($campaign, "Created campaign: {$campaign->name}");

        return redirect()
            ->route('admin.campaigns.show', $campaign)
            ->with('success', 'Campaign created.');
    }

    public function show(Campaign $campaign): View
    {
        $this->authorize('view', $campaign);

        $campaign->load(['owner', 'products', 'contentItems' => fn ($q) => $q->latest()->limit(10)]);

        return view('admin.campaigns.show', compact('campaign'));
    }

    public function edit(Campaign $campaign): View
    {
        $this->authorize('update', $campaign);

        $campaign->load('products');

        return view('admin.campaigns.edit', array_merge($this->formData(), [
            'campaign' => $campaign,
            'selectedProductIds' => $campaign->products->pluck('id')->all(),
        ]));
    }

    public function update(Request $request, Campaign $campaign): RedirectResponse
    {
        $this->authorize('update', $campaign);

        $data = $this->validated($request);
        $productIds = $data['product_ids'] ?? [];
        unset($data['product_ids']);

        $old = $campaign->getAttributes();
        $campaign->update($data);
        $campaign->products()->sync($productIds);

        ActivityLogService::updated($campaign, $old, "Updated campaign: {$campaign->name}");

        return redirect()
            ->route('admin.campaigns.show', $campaign)
            ->with('success', 'Campaign updated.');
    }

    public function destroy(Campaign $campaign): RedirectResponse
    {
        $this->authorize('delete', $campaign);

        $name = $campaign->name;
        ActivityLogService::deleted($campaign, "Deleted campaign: {$name}");
        $campaign->delete();

        return redirect()
            ->route('admin.campaigns.index')
            ->with('success', 'Campaign deleted.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'objective' => ['nullable', 'string', 'max:255'],
            'owner_id' => ['nullable', 'integer', 'exists:users,id'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'status' => ['required', 'string', 'in:draft,active,paused,completed,cancelled'],
            'audience_notes' => ['nullable', 'string'],
            'budget_amount' => ['nullable', 'numeric', 'min:0'],
            'utm_campaign' => ['nullable', 'string', 'max:255'],
            'product_ids' => ['nullable', 'array'],
            'product_ids.*' => ['integer', 'exists:products,id'],
        ]);
    }

    private function formData(): array
    {
        $user = auth()->user();

        return [
            'products' => Product::query()->visibleTo($user)->ordered()->get(['id', 'name']),
            'owners' => User::orderBy('name')->get(['id', 'name']),
            'statuses' => ['draft', 'active', 'paused', 'completed', 'cancelled'],
        ];
    }
}
