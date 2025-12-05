<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Position;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PositionController extends Controller
{
    public function index(): View
    {
        $positions = Position::ordered()->paginate(15);

        return view('admin.positions.index', compact('positions'));
    }

    public function create(): View
    {
        return view('admin.positions.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'type' => ['required', 'string', 'in:Full-time,Part-time,Contract'],
            'location' => ['required', 'string', 'in:Remote,Hybrid,On-site'],
            'tags' => ['nullable', 'string', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
            'order' => ['nullable', 'integer', 'min:0'],
        ]);

        $data['is_active'] = $request->boolean('is_active', true);
        $data['order'] = $data['order'] ?? 0;

        Position::create($data);

        return redirect()
            ->route('admin.positions.index')
            ->with('success', 'Position created successfully.');
    }

    public function edit(Position $position): View
    {
        return view('admin.positions.edit', compact('position'));
    }

    public function update(Request $request, Position $position): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'type' => ['required', 'string', 'in:Full-time,Part-time,Contract'],
            'location' => ['required', 'string', 'in:Remote,Hybrid,On-site'],
            'tags' => ['nullable', 'string', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
            'order' => ['nullable', 'integer', 'min:0'],
        ]);

        $data['is_active'] = $request->boolean('is_active', true);
        $data['order'] = $data['order'] ?? $position->order;

        $position->update($data);

        return redirect()
            ->route('admin.positions.index')
            ->with('success', 'Position updated successfully.');
    }

    public function destroy(Position $position): RedirectResponse
    {
        $position->delete();

        return redirect()
            ->route('admin.positions.index')
            ->with('success', 'Position deleted successfully.');
    }
}



