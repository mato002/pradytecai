<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DemoRequest;
use App\Models\Product;
use App\Services\ActivityLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DemoRequestController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()->can('demo_requests.view'), 403);

        $user = $request->user();
        $scoped = $user->scopedProductIds();

        $query = DemoRequest::query()
            ->with(['product', 'assignee', 'contactMessage'])
            ->latest();

        if ($scoped !== null) {
            $query->where(function ($q) use ($scoped) {
                $q->whereNull('product_id')->orWhereIn('product_id', $scoped);
            });
        }

        if ($productId = session('marketing.product_id')) {
            $query->where('product_id', $productId);
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $demos = $query->paginate(20)->withQueryString();

        return view('admin.demos.index', [
            'demos' => $demos,
            'statuses' => DemoRequest::STATUSES,
            'products' => Product::query()->visibleTo($user)->ordered()->get(['id', 'name']),
        ]);
    }

    public function show(DemoRequest $demo): View
    {
        abort_unless(auth()->user()->can('demo_requests.view'), 403);
        $this->assertVisible($demo);

        $demo->load(['product', 'assignee', 'contactMessage', 'campaign']);

        return view('admin.demos.show', [
            'demo' => $demo,
            'statuses' => DemoRequest::STATUSES,
        ]);
    }

    public function updateStatus(Request $request, DemoRequest $demo): RedirectResponse
    {
        return $this->update($request, $demo);
    }

    public function update(Request $request, DemoRequest $demo): RedirectResponse
    {
        abort_unless(auth()->user()->can('demo_requests.manage'), 403);
        $this->assertVisible($demo);

        $data = $request->validate([
            'status' => ['required', 'string', 'in:'.implode(',', DemoRequest::STATUSES)],
            'notes' => ['nullable', 'string'],
            'scheduled_at' => ['nullable', 'date'],
            'assigned_to' => ['nullable', 'integer', 'exists:users,id'],
        ]);

        $old = $demo->status;
        $demo->update($data);

        ActivityLogService::updated($demo, ['status' => $old], "Demo status {$old} → {$data['status']}");

        return redirect()
            ->route('admin.demos.show', $demo)
            ->with('success', 'Demo updated.');
    }

    private function assertVisible(DemoRequest $demo): void
    {
        $user = auth()->user();
        if (! $user->canAccessProduct($demo->product_id)) {
            abort(403);
        }
    }
}
