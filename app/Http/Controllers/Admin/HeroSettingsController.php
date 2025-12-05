<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HeroSettingsController extends Controller
{
    public function edit(): View
    {
        $heroImagePath = SiteSetting::get('hero_background_image');
        $heroImageUrl = $heroImagePath ? asset('storage/' . ltrim($heroImagePath, '/')) : null;

        return view('admin.settings.hero', compact('heroImagePath', 'heroImageUrl'));
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'hero_image' => ['nullable', 'image', 'max:4096'],
        ]);

        if ($request->hasFile('hero_image')) {
            $path = $request->file('hero_image')->store('hero', 'public');
            SiteSetting::set('hero_background_image', $path);
        }

        return redirect()
            ->route('admin.settings.hero.edit')
            ->with('success', 'Hero background updated.');
    }
}



