<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Integration;
use App\Models\Product;
use App\Models\SocialAccount;
use App\Services\ActivityLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SocialAccountController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', SocialAccount::class);

        $query = SocialAccount::query()
            ->with(['product', 'integration'])
            ->visibleTo($request->user())
            ->latest();

        if ($productId = session('marketing.product_id')) {
            $query->where('product_id', $productId);
        }

        if ($request->filled('platform')) {
            $query->where('platform', $request->platform);
        }

        $accounts = $query->paginate(20)->withQueryString();

        return view('admin.social-accounts.index', compact('accounts'));
    }

    public function create(): View
    {
        $this->authorize('create', SocialAccount::class);

        return view('admin.social-accounts.create', $this->formData());
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', SocialAccount::class);

        $data = $this->validated($request);
        $account = SocialAccount::create($data);

        ActivityLogService::created($account, "Created social account: {$account->name}");

        return redirect()
            ->route('admin.social-accounts.show', $account)
            ->with('success', 'Social account created.');
    }

    public function show(SocialAccount $socialAccount): View
    {
        $this->authorize('view', $socialAccount);

        $socialAccount->load(['product', 'integration', 'contentDestinations' => fn ($q) => $q->latest()->limit(20)]);

        return view('admin.social-accounts.show', ['account' => $socialAccount]);
    }

    public function edit(SocialAccount $socialAccount): View
    {
        $this->authorize('update', $socialAccount);

        return view('admin.social-accounts.edit', array_merge($this->formData(), [
            'account' => $socialAccount,
        ]));
    }

    public function update(Request $request, SocialAccount $socialAccount): RedirectResponse
    {
        $this->authorize('update', $socialAccount);

        $data = $this->validated($request);
        $old = $socialAccount->getAttributes();
        $socialAccount->update($data);

        ActivityLogService::updated($socialAccount, $old, "Updated social account: {$socialAccount->name}");

        return redirect()
            ->route('admin.social-accounts.show', $socialAccount)
            ->with('success', 'Social account updated.');
    }

    public function destroy(SocialAccount $socialAccount): RedirectResponse
    {
        $this->authorize('delete', $socialAccount);

        ActivityLogService::deleted($socialAccount, "Deleted social account: {$socialAccount->name}");
        $socialAccount->delete();

        return redirect()
            ->route('admin.social-accounts.index')
            ->with('success', 'Social account deleted.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'integration_id' => ['nullable', 'integer', 'exists:integrations,id'],
            'product_id' => ['nullable', 'integer', 'exists:products,id'],
            'platform' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'external_id' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'string', 'in:active,inactive,error'],
            'token_status' => ['nullable', 'string', 'in:valid,expired,unknown,revoked'],
            'token_expires_at' => ['nullable', 'date'],
            'follower_count' => ['nullable', 'integer', 'min:0'],
            'can_publish' => ['sometimes', 'boolean'],
            'can_analytics' => ['sometimes', 'boolean'],
            'can_inbox' => ['sometimes', 'boolean'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $data['can_publish'] = $request->boolean('can_publish', true);
        $data['can_analytics'] = $request->boolean('can_analytics', true);
        $data['can_inbox'] = $request->boolean('can_inbox', false);
        $data['is_active'] = $request->boolean('is_active', true);
        $data['token_status'] = $data['token_status'] ?? 'unknown';

        return $data;
    }

    private function formData(): array
    {
        $user = auth()->user();

        return [
            'products' => Product::query()->visibleTo($user)->ordered()->get(['id', 'name']),
            'integrations' => Integration::orderBy('provider')->get(['id', 'provider', 'external_account_name']),
            'platforms' => ['facebook', 'instagram', 'linkedin', 'twitter', 'tiktok', 'youtube', 'other'],
        ];
    }
}
