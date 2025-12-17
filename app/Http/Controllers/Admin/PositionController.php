<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Position;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PositionController extends Controller
{
    public function index(Request $request): View
    {
        $query = Position::query()->ordered();

        // Filters
        if ($request->filled('status') && in_array($request->string('status'), ['active', 'inactive', 'all'], true)) {
            $status = $request->string('status');

            if ($status === 'active') {
                $query->where('is_active', true);
            } elseif ($status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        if ($request->filled('type')) {
            $query->where('type', $request->string('type'));
        }

        if ($request->filled('location')) {
            $query->where('location', $request->string('location'));
        }

        if ($request->filled('q')) {
            $search = $request->string('q');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%')
                    ->orWhere('tags', 'like', '%' . $search . '%');
            });
        }

        $positions = $query->paginate(15)->withQueryString();

        // Simple stats for header cards
        $stats = [
            'total' => Position::count(),
            'active' => Position::where('is_active', true)->count(),
            'remote' => Position::where('location', 'Remote')->count(),
            'hybrid' => Position::where('location', 'Hybrid')->count(),
            'onsite' => Position::where('location', 'On-site')->count(),
        ];

        $filters = [
            'status' => $request->string('status', 'active'),
            'type' => $request->string('type'),
            'location' => $request->string('location'),
            'q' => $request->string('q'),
        ];

        return view('admin.positions.index', compact('positions', 'stats', 'filters'));
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

    public function show(Position $position): View
    {
        return view('admin.positions.show', compact('position'));
    }

    public function destroy(Position $position): RedirectResponse
    {
        $position->delete();

        return redirect()
            ->route('admin.positions.index')
            ->with('success', 'Position deleted successfully.');
    }
}



