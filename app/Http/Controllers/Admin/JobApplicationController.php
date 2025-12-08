<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JobApplication;
use App\Services\CommunicationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class JobApplicationController extends Controller
{
    /**
     * Display a listing of job applications
     */
    public function index(Request $request): View
    {
        $query = JobApplication::with('position')->recent();

        // Filter by status if provided
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter by position if provided
        if ($request->has('position_id') && $request->position_id) {
            $query->where('position_id', $request->position_id);
        }

        $applications = $query->paginate(20);

        $statusCounts = [
            'all' => JobApplication::count(),
            'pending' => JobApplication::where('status', 'pending')->count(),
            'reviewing' => JobApplication::where('status', 'reviewing')->count(),
            'shortlisted' => JobApplication::where('status', 'shortlisted')->count(),
            'interviewed' => JobApplication::where('status', 'interviewed')->count(),
            'accepted' => JobApplication::where('status', 'accepted')->count(),
            'rejected' => JobApplication::where('status', 'rejected')->count(),
        ];

        $positions = \App\Models\Position::where('is_active', true)->get();

        return view('admin.applications.index', compact('applications', 'statusCounts', 'positions'));
    }

    /**
     * Display the specified job application
     */
    public function show(JobApplication $application): View
    {
        $application->load('position');
        return view('admin.applications.show', compact('application'));
    }

    /**
     * Update application status
     */
    public function updateStatus(Request $request, JobApplication $application): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,reviewing,shortlisted,interviewed,accepted,rejected',
            'admin_notes' => 'nullable|string',
            'rating' => 'nullable|integer|min:1|max:5',
        ]);

        $application->update($validated);

        return redirect()->route('admin.applications.show', $application)
            ->with('success', 'Application status updated successfully.');
    }

    /**
     * Schedule an interview
     */
    public function scheduleInterview(Request $request, JobApplication $application): RedirectResponse
    {
        $validated = $request->validate([
            'interview_scheduled_at' => 'required|date|after:now',
            'interview_notes' => 'nullable|string',
        ]);

        $application->update([
            'interview_scheduled_at' => $validated['interview_scheduled_at'],
            'interview_notes' => $validated['interview_notes'] ?? null,
            'status' => 'interviewed',
        ]);

        return redirect()->route('admin.applications.show', $application)
            ->with('success', 'Interview scheduled successfully.');
    }

    /**
     * Update interview notes
     */
    public function updateInterviewNotes(Request $request, JobApplication $application): RedirectResponse
    {
        $validated = $request->validate([
            'interview_notes' => 'required|string',
        ]);

        $application->update($validated);

        return redirect()->route('admin.applications.show', $application)
            ->with('success', 'Interview notes updated successfully.');
    }

    /**
     * Download resume
     */
    public function downloadResume(JobApplication $application)
    {
        if (!$application->resume_path) {
            abort(404, 'Resume not found');
        }

        return \Storage::disk('public')->download($application->resume_path);
    }

    /**
     * Send message to applicant
     */
    public function sendMessage(Request $request, JobApplication $application, CommunicationService $communicationService): RedirectResponse
    {
        $validated = $request->validate([
            'channels' => 'required|array|min:1',
            'channels.*' => 'required|in:email,sms,whatsapp',
            'message' => 'required|string|min:10',
            'subject' => 'nullable|string|max:255',
        ]);

        $results = $communicationService->sendMultiple(
            $application,
            $validated['channels'],
            $validated['message'],
            $validated['subject'] ?? null
        );

        $successCount = count(array_filter($results));
        $totalCount = count($results);

        if ($successCount === $totalCount) {
            $message = "Message sent successfully via " . implode(', ', array_map('ucfirst', $validated['channels']));
        } elseif ($successCount > 0) {
            $message = "Message sent via " . $successCount . " of " . $totalCount . " channel(s). Some channels may have failed.";
        } else {
            $message = "Failed to send message. Please try again or check the logs.";
        }

        return redirect()->route('admin.applications.show', $application)
            ->with($successCount > 0 ? 'success' : 'error', $message);
    }
}


