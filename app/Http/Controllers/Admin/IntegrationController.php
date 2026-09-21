<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Integration;
use App\Services\ActivityLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class IntegrationController extends Controller
{
    public function index(): View
    {
        abort_unless(auth()->user()->can('integrations.view'), 403);

        $integrations = Integration::query()->latest()->get();

        return view('admin.integrations.index', compact('integrations'));
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless(auth()->user()->can('integrations.manage'), 403);

        $data = $request->validate([
            'provider' => ['required', 'string', 'max:255'],
            'external_account_name' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'string', 'in:connected,disconnected,error,expired'],
        ]);

        $integration = Integration::create($data);

        ActivityLogService::created($integration, "Connected integration: {$integration->provider}");

        return redirect()
            ->route('admin.integrations.index')
            ->with('success', 'Integration saved.');
    }

    public function reconnect(Integration $integration): RedirectResponse
    {
        abort_unless(auth()->user()->can('integrations.manage'), 403);

        $hasCredentials = filled($integration->external_account_name)
            || filled($integration->getRawOriginal('access_token'));

        $integration->update([
            'last_error' => $hasCredentials ? null : 'No credentials available for reconnect.',
            'status' => $hasCredentials ? 'connected' : 'error',
        ]);

        ActivityLogService::custom('updated', $integration, "Reconnected integration: {$integration->provider}");

        return redirect()
            ->route('admin.integrations.index')
            ->with('success', 'Integration reconnect attempted.');
    }

    public function destroy(Integration $integration): RedirectResponse
    {
        abort_unless(auth()->user()->can('integrations.manage'), 403);

        ActivityLogService::deleted($integration, "Removed integration: {$integration->provider}");
        $integration->delete();

        return redirect()
            ->route('admin.integrations.index')
            ->with('success', 'Integration removed.');
    }
}
