<?php

namespace App\Http\Controllers;

use App\Models\JobApplication;
use App\Models\Position;
use App\Services\ActivityLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class JobApplicationController extends Controller
{
    /**
     * Show the application form for a specific position
     */
    public function create(Request $request, Position $position): View
    {
        if (!$position->is_active) {
            abort(404, 'This position is no longer available.');
        }

        return view('careers.apply', compact('position'));
    }

    /**
     * Store a new job application
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'position_id' => 'required|exists:positions,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:255',
            'cover_letter' => 'required|string|min:50',
            'resume' => 'required|file|mimes:pdf,doc,docx|max:5120', // 5MB max
        ]);

        // Check for duplicate application (same email and position)
        $duplicate = JobApplication::where('email', $validated['email'])
            ->where('position_id', $validated['position_id'])
            ->exists();

        if ($duplicate) {
            return back()
                ->withInput()
                ->withErrors(['email' => 'You have already applied for this position.']);
        }

        // Handle resume upload
        if ($request->hasFile('resume')) {
            $resumePath = $request->file('resume')->store('resumes', 'public');
            $validated['resume_path'] = $resumePath;
        }

        unset($validated['resume']);

        $application = JobApplication::create($validated);

        // Log the application creation
        ActivityLogService::created($application, "New application submitted by {$application->name} for {$application->position->title}");

        return redirect()->route('careers.index')
            ->with('success', 'Thank you! Your application has been submitted successfully. We will review it and get back to you soon.');
    }
}

