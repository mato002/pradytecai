<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JobApplication;
use App\Services\CommunicationService;
use App\Services\ActivityLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class JobApplicationController extends Controller
{
    /**
     * Display a listing of job applications
     */
    public function index(Request $request)
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

        // Filter by rating if provided
        if ($request->has('rating') && $request->rating) {
            $query->where('rating', $request->rating);
        }

        // Filter by date range if provided
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('cover_letter', 'like', "%{$search}%")
                    ->orWhereHas('position', function ($positionQuery) use ($search) {
                        $positionQuery->where('title', 'like', "%{$search}%");
                    });
            });
        }

        // Handle export
        if ($request->has('export') && $request->export === 'csv') {
            // If specific IDs are provided, filter by them
            if ($request->has('ids') && $request->ids) {
                $ids = explode(',', $request->ids);
                $query->whereIn('id', $ids);
            }
            return $this->exportCsv($query->get());
        }

        $applications = $query->paginate(20)->withQueryString();

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

        // Calculate statistics
        $stats = [
            'total' => JobApplication::count(),
            'this_month' => JobApplication::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
            'last_month' => JobApplication::whereMonth('created_at', now()->subMonth()->month)
                ->whereYear('created_at', now()->subMonth()->year)
                ->count(),
            'this_week' => JobApplication::where('created_at', '>=', now()->startOfWeek())->count(),
            'avg_rating' => JobApplication::whereNotNull('rating')->avg('rating'),
            'accepted_count' => JobApplication::where('status', 'accepted')->count(),
            'rejected_count' => JobApplication::where('status', 'rejected')->count(),
            'pending_review' => JobApplication::whereIn('status', ['pending', 'reviewing'])->count(),
            'by_position' => JobApplication::selectRaw('position_id, count(*) as count')
                ->with('position')
                ->groupBy('position_id')
                ->orderByDesc('count')
                ->limit(5)
                ->get(),
            'recent_activity' => \App\Models\ActivityLog::where('model_type', JobApplication::class)
                ->with('user')
                ->orderByDesc('created_at')
                ->limit(5)
                ->get(),
        ];

        // Calculate conversion rates
        $totalProcessed = $stats['accepted_count'] + $stats['rejected_count'];
        $stats['acceptance_rate'] = $totalProcessed > 0 
            ? round(($stats['accepted_count'] / $totalProcessed) * 100, 1) 
            : 0;
        $stats['rejection_rate'] = $totalProcessed > 0 
            ? round(($stats['rejected_count'] / $totalProcessed) * 100, 1) 
            : 0;

        return view('admin.applications.index', compact('applications', 'statusCounts', 'positions', 'stats'));
    }

    /**
     * Export applications to CSV
     */
    private function exportCsv($applications)
    {
        $filename = 'job_applications_' . date('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($applications) {
            $file = fopen('php://output', 'w');
            
            // Add CSV headers
            fputcsv($file, [
                'ID', 'Name', 'Email', 'Phone', 'Position', 'Status', 
                'Cover Letter', 'Applied At', 'Rating', 'Interview Scheduled At'
            ]);

            // Add data rows
            foreach ($applications as $application) {
                fputcsv($file, [
                    $application->id,
                    $application->name,
                    $application->email,
                    $application->phone ?? '',
                    $application->position->title ?? '',
                    $application->status,
                    $application->cover_letter ?? '',
                    $application->created_at?->format('Y-m-d H:i:s'),
                    $application->rating ?? '',
                    $application->interview_scheduled_at?->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Handle bulk actions
     */
    public function bulkAction(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'action' => 'required|string',
            'ids' => 'required|string',
        ]);

        $ids = explode(',', $validated['ids']);
        $applications = JobApplication::whereIn('id', $ids)->get();

        if ($applications->isEmpty()) {
            return redirect()->route('admin.applications.index')
                ->with('error', 'No applications selected.');
        }

        $action = $validated['action'];
        $count = 0;

        if (str_starts_with($action, 'status:')) {
            $newStatus = str_replace('status:', '', $action);
            foreach ($applications as $application) {
                $oldStatus = $application->status;
                $application->update(['status' => $newStatus]);
                
                // Log the bulk status change
                ActivityLogService::updated($application, ['status' => $oldStatus], 
                    "Bulk updated status from {$oldStatus} to {$newStatus}");
                
                // Send notification
                try {
                    $this->sendStatusChangeNotification($application, $oldStatus, $newStatus);
                } catch (\Exception $e) {
                    \Log::error('Failed to send bulk status notification', [
                        'application_id' => $application->id,
                        'error' => $e->getMessage(),
                    ]);
                }
                $count++;
            }
            $message = "Updated status for {$count} application(s) to " . ucfirst($newStatus);
        } elseif ($action === 'delete') {
            foreach ($applications as $application) {
                ActivityLogService::deleted($application, "Bulk deleted application from {$application->name}");
                $application->delete();
                $count++;
            }
            $message = "Deleted {$count} application(s)";
        } else {
            return redirect()->route('admin.applications.index')
                ->with('error', 'Invalid action.');
        }

        return redirect()->route('admin.applications.index')
            ->with('success', $message);
    }

    /**
     * Display the specified job application
     */
    public function show(JobApplication $application): View
    {
        $application->load('position');
        
        // Log the view
        ActivityLogService::viewed($application, "Viewed application from {$application->name}");
        
        // Load activity logs for this application
        $activityLogs = \App\Models\ActivityLog::where('model_type', JobApplication::class)
            ->where('model_id', $application->id)
            ->with('user')
            ->orderByDesc('created_at')
            ->get();

        // Load comments for this application
        $comments = \App\Models\ApplicationComment::where('job_application_id', $application->id)
            ->whereNull('parent_id')
            ->with(['user', 'replies.user'])
            ->orderByDesc('created_at')
            ->get();
        
        return view('admin.applications.show', compact('application', 'activityLogs', 'comments'));
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

        $oldValues = [
            'status' => $application->status,
            'rating' => $application->rating,
            'admin_notes' => $application->admin_notes,
        ];

        $application->update($validated);

        // Log the status change
        $description = "Updated application status from {$oldValues['status']} to {$validated['status']}";
        if (isset($validated['rating']) && $validated['rating'] != $oldValues['rating']) {
            $description .= " and rating to {$validated['rating']}";
        }
        ActivityLogService::updated($application, $oldValues, $description);

        // Send email notification if status changed
        if ($oldValues['status'] !== $validated['status']) {
            $this->sendStatusChangeNotification($application, $oldValues['status'], $validated['status']);
        }

        return redirect()->route('admin.applications.show', $application)
            ->with('success', 'Application status updated successfully.');
    }

    /**
     * Schedule an interview
     */
    public function scheduleInterview(Request $request, JobApplication $application): RedirectResponse
    {
        // Only allow scheduling interviews for reviewing, shortlisted, or interviewed statuses
        // This ensures proper workflow order: pending → reviewing → shortlisted → interviewed → accepted
        // Rejected applications cannot proceed further
        if (!in_array($application->status, ['reviewing', 'shortlisted', 'interviewed'])) {
            $message = match($application->status) {
                'rejected' => 'Cannot schedule interview for rejected applications.',
                'accepted' => 'Cannot schedule interview for accepted applications.',
                'pending' => 'Please move the application to "Reviewing" or "Shortlisted" status before scheduling an interview.',
                default => 'Interview scheduling is not available for this application status.',
            };
            
            return redirect()->route('admin.applications.show', $application)
                ->with('error', $message);
        }

        $validated = $request->validate([
            'interview_scheduled_at' => 'required|date|after:now',
            'interview_notes' => 'nullable|string',
        ]);

        $oldValues = [
            'interview_scheduled_at' => $application->interview_scheduled_at,
            'interview_notes' => $application->interview_notes,
            'status' => $application->status,
        ];

        $application->update([
            'interview_scheduled_at' => $validated['interview_scheduled_at'],
            'interview_notes' => $validated['interview_notes'] ?? null,
            'status' => 'interviewed',
        ]);

        // Log the interview scheduling
        ActivityLogService::custom('interview_scheduled', $application, 
            "Scheduled interview for {$application->name} on " . 
            \Carbon\Carbon::parse($validated['interview_scheduled_at'])->format('F j, Y \a\t g:i A'));

        // Send email notification
        $this->sendInterviewScheduledNotification($application);

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

        $oldValues = ['interview_notes' => $application->interview_notes];
        $application->update($validated);

        // Log the notes update
        ActivityLogService::updated($application, $oldValues, "Updated interview notes for {$application->name}");

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

        // Log the communication
        $channelsSent = array_keys(array_filter($results));
        ActivityLogService::custom('message_sent', $application, 
            "Sent message to {$application->name} via " . implode(', ', array_map('ucfirst', $channelsSent)));

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

    /**
     * Add a comment to an application
     */
    public function addComment(Request $request, JobApplication $application): RedirectResponse
    {
        $validated = $request->validate([
            'comment' => 'required|string|min:3',
            'parent_id' => 'nullable|exists:application_comments,id',
            'is_internal' => 'nullable|boolean',
        ]);

        $comment = \App\Models\ApplicationComment::create([
            'job_application_id' => $application->id,
            'user_id' => auth()->id(),
            'parent_id' => $validated['parent_id'] ?? null,
            'comment' => $validated['comment'],
            'is_internal' => $request->boolean('is_internal', false),
        ]);

        // Log the comment
        ActivityLogService::custom('comment_added', $application, 
            "Added " . ($comment->is_internal ? 'internal ' : '') . "comment: " . substr($validated['comment'], 0, 50));

        return redirect()->route('admin.applications.show', $application)
            ->with('success', 'Comment added successfully.');
    }

    /**
     * Delete a comment
     */
    public function deleteComment(\App\Models\ApplicationComment $comment): RedirectResponse
    {
        $application = $comment->jobApplication;
        
        // Only allow deletion by comment author or admin
        if ($comment->user_id !== auth()->id()) {
            return redirect()->route('admin.applications.show', $application)
                ->with('error', 'You can only delete your own comments.');
        }

        ActivityLogService::custom('comment_deleted', $application, 
            "Deleted comment from {$comment->user->name}");
        
        $comment->delete();

        return redirect()->route('admin.applications.show', $application)
            ->with('success', 'Comment deleted successfully.');
    }

    /**
     * Send email notification on status change
     */
    private function sendStatusChangeNotification(JobApplication $application, string $oldStatus, string $newStatus): void
    {
        try {
            $statusMessages = [
                'reviewing' => 'Your application is now under review. We will get back to you soon.',
                'shortlisted' => 'Congratulations! Your application has been shortlisted. We will contact you for the next steps.',
                'interviewed' => 'Your application has progressed to the interview stage. We will contact you to schedule an interview.',
                'accepted' => 'Congratulations! Your application has been accepted. We are excited to have you join our team!',
                'rejected' => 'Thank you for your interest. Unfortunately, we are unable to proceed with your application at this time.',
            ];

            $message = $statusMessages[$newStatus] ?? "Your application status has been updated to: " . ucfirst($newStatus);
            $subject = "Application Status Update - {$application->position->title}";

            $communicationService = app(CommunicationService::class);
            $communicationService->sendEmail($application, $message, $subject);
        } catch (\Exception $e) {
            \Log::error('Failed to send status change notification', [
                'application_id' => $application->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Send email notification for scheduled interview
     */
    private function sendInterviewScheduledNotification(JobApplication $application): void
    {
        try {
            $interviewDate = $application->interview_scheduled_at->format('F j, Y \a\t g:i A');
            $message = "Your interview has been scheduled for {$interviewDate}. Please confirm your availability.";
            $subject = "Interview Scheduled - {$application->position->title}";

            $communicationService = app(CommunicationService::class);
            $communicationService->sendEmail($application, $message, $subject);
        } catch (\Exception $e) {
            \Log::error('Failed to send interview notification', [
                'application_id' => $application->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}


