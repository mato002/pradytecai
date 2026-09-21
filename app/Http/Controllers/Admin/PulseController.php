<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MarketingAlert;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PulseController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()->can('pulse.view'), 403);

        $query = MarketingAlert::query()
            ->with(['product', 'socialAccount', 'campaign'])
            ->latest('triggered_at');

        if ($productId = session('marketing.product_id')) {
            $query->where('product_id', $productId);
        }

        $status = $request->query('status', 'open');
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($request->filled('severity') && $request->severity !== 'all') {
            $query->where('severity', $request->severity);
        }

        $alerts = $query->paginate(30)->withQueryString();

        return view('admin.pulse.index', compact('alerts'));
    }

    public function resolve(MarketingAlert $alert): RedirectResponse
    {
        abort_unless(auth()->user()->can('pulse.view'), 403);

        $alert->update([
            'status' => 'resolved',
            'resolved_at' => now(),
        ]);

        return redirect()
            ->route('admin.pulse.index')
            ->with('success', 'Alert resolved.');
    }
}
