<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class SettingsController extends Controller
{
    public function index(): View
    {
        return view('admin.settings.index');
    }

    public function general(): View
    {
        return view('admin.settings.general');
    }

    public function updateGeneral(Request $request): RedirectResponse
    {
        // Placeholder for general settings updates
        // You can add SiteSetting model updates here
        
        return redirect()
            ->route('admin.settings.general')
            ->with('success', 'General settings updated successfully.');
    }
}

