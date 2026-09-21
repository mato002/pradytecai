<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function index(): View
    {
        abort_unless(auth()->user()->can('settings.view'), 403);

        return view('admin.settings.index');
    }

    public function general(): View
    {
        abort_unless(auth()->user()->can('settings.view'), 403);

        $settings = [
            'site_name' => SiteSetting::get('site_name', 'Pradytecai'),
            'site_description' => SiteSetting::get('site_description', ''),
            'contact_email' => SiteSetting::get('contact_email', ''),
            'contact_phone' => SiteSetting::get('contact_phone', ''),
            'maintenance_mode' => SiteSetting::get('maintenance_mode', '0'),
            'content_approval_required' => SiteSetting::get('content.approval_required', '1'),
            'pulse_social_inactive_days' => SiteSetting::get('pulse.social_inactive_days', '5'),
            'pulse_product_not_marketed_days' => SiteSetting::get('pulse.product_not_marketed_days', '14'),
        ];

        return view('admin.settings.general', compact('settings'));
    }

    public function updateGeneral(Request $request): RedirectResponse
    {
        abort_unless(auth()->user()->can('settings.manage'), 403);

        $data = $request->validate([
            'site_name' => ['required', 'string', 'max:255'],
            'site_description' => ['nullable', 'string', 'max:1000'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:255'],
            'maintenance_mode' => ['sometimes', 'boolean'],
            'content_approval_required' => ['sometimes', 'boolean'],
            'pulse_social_inactive_days' => ['nullable', 'integer', 'min:1', 'max:90'],
            'pulse_product_not_marketed_days' => ['nullable', 'integer', 'min:1', 'max:180'],
        ]);

        SiteSetting::set('site_name', $data['site_name']);
        SiteSetting::set('site_description', $data['site_description'] ?? '');
        SiteSetting::set('contact_email', $data['contact_email'] ?? '');
        SiteSetting::set('contact_phone', $data['contact_phone'] ?? '');
        SiteSetting::set('maintenance_mode', $request->boolean('maintenance_mode') ? '1' : '0');
        SiteSetting::set('content.approval_required', $request->boolean('content_approval_required', true) ? '1' : '0');
        SiteSetting::set('pulse.social_inactive_days', (string) ($data['pulse_social_inactive_days'] ?? 5));
        SiteSetting::set('pulse.product_not_marketed_days', (string) ($data['pulse_product_not_marketed_days'] ?? 14));

        return redirect()
            ->route('admin.settings.general')
            ->with('success', 'General settings updated successfully.');
    }
}
