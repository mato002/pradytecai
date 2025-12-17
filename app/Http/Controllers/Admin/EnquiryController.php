<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Services\CommunicationService;
use App\Services\ActivityLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EnquiryController extends Controller
{
    /**
     * Display a listing of enquiries
     */
    public function index(Request $request)
    {
        $query = ContactMessage::latest();

        // Filter by status if provided
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter by topic if provided
        if ($request->has('topic') && $request->topic) {
            $query->where('topic', $request->topic);
        }

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('subject', 'like', "%{$search}%")
                    ->orWhere('message', 'like', "%{$search}%");
            });
        }

        // Handle export
        if ($request->has('export') && $request->export === 'csv') {
            return $this->exportCsv($query->get());
        }

        $messages = $query->paginate(20);

        $statusCounts = [
            'all' => ContactMessage::count(),
            'new' => ContactMessage::where('status', 'new')->count(),
            'read' => ContactMessage::where('status', 'read')->count(),
            'responded' => ContactMessage::where('status', 'responded')->count(),
            'archived' => ContactMessage::where('status', 'archived')->count(),
        ];

        $topics = ContactMessage::whereNotNull('topic')
            ->distinct()
            ->pluck('topic')
            ->filter()
            ->values();

        return view('admin.enquiries.index', compact('messages', 'statusCounts', 'topics'));
    }

    /**
     * Export enquiries to CSV
     */
    private function exportCsv($enquiries)
    {
        $filename = 'enquiries_' . date('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($enquiries) {
            $file = fopen('php://output', 'w');
            
            // Add CSV headers
            fputcsv($file, [
                'ID', 'Name', 'Company', 'Email', 'Phone', 'Topic', 'Subject', 
                'Message', 'Status', 'Received At', 'Read At', 'Responded At'
            ]);

            // Add data rows
            foreach ($enquiries as $enquiry) {
                fputcsv($file, [
                    $enquiry->id,
                    $enquiry->name,
                    $enquiry->company ?? '',
                    $enquiry->email,
                    $enquiry->phone ?? '',
                    $enquiry->topic ?? '',
                    $enquiry->subject,
                    $enquiry->message,
                    $enquiry->status,
                    $enquiry->created_at?->format('Y-m-d H:i:s'),
                    $enquiry->read_at?->format('Y-m-d H:i:s'),
                    $enquiry->responded_at?->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Display the specified enquiry
     */
    public function show(ContactMessage $enquiry): View
    {
        // Mark as read if not already read
        if (!$enquiry->read_at) {
            $enquiry->update([
                'read_at' => now(),
                'read_by' => auth()->id(),
                'status' => $enquiry->status === 'new' ? 'read' : $enquiry->status,
            ]);
            ActivityLogService::viewed($enquiry, "Viewed enquiry from {$enquiry->name}");
        }

        $enquiry->load(['readBy', 'respondedBy']);

        return view('admin.enquiries.show', compact('enquiry'));
    }

    /**
     * Update enquiry status
     */
    public function updateStatus(Request $request, ContactMessage $enquiry): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:new,read,responded,archived',
            'admin_notes' => 'nullable|string',
        ]);

        $updateData = ['status' => $validated['status']];

        if (isset($validated['admin_notes'])) {
            $updateData['admin_notes'] = $validated['admin_notes'];
        }

        // Set responded_at if status is being changed to responded
        if ($validated['status'] === 'responded' && !$enquiry->responded_at) {
            $updateData['responded_at'] = now();
            $updateData['responded_by'] = auth()->id();
        }

        $oldStatus = $enquiry->status;
        $enquiry->update($updateData);

        ActivityLogService::updated($enquiry, ['status' => $oldStatus], "Updated enquiry status from {$oldStatus} to {$validated['status']}");

        return redirect()->route('admin.enquiries.show', $enquiry)
            ->with('success', 'Enquiry status updated successfully.');
    }

    /**
     * Reply to an enquiry
     */
    public function reply(Request $request, ContactMessage $enquiry): RedirectResponse
    {
        $validated = $request->validate([
            'reply_subject' => 'required|string|max:255',
            'reply_message' => 'required|string',
        ]);

        try {
            $communicationService = app(CommunicationService::class);
            
            $communicationService->sendEmailToRecipient(
                $enquiry->email,
                $validated['reply_subject'],
                $validated['reply_message'],
                $enquiry->name
            );

            // Update enquiry status
            $enquiry->update([
                'status' => 'responded',
                'responded_at' => now(),
                'responded_by' => auth()->id(),
            ]);

            ActivityLogService::custom('replied', $enquiry, "Replied to enquiry from {$enquiry->name}");

            return redirect()->route('admin.enquiries.show', $enquiry)
                ->with('success', 'Reply sent successfully.');
        } catch (\Exception $e) {
            return redirect()->route('admin.enquiries.show', $enquiry)
                ->with('error', 'Failed to send reply: ' . $e->getMessage());
        }
    }

    /**
     * Delete an enquiry
     */
    public function destroy(ContactMessage $enquiry): RedirectResponse
    {
        $enquiryName = $enquiry->name;
        ActivityLogService::deleted($enquiry, "Deleted enquiry from {$enquiryName}");
        
        $enquiry->delete();

        return redirect()->route('admin.enquiries.index')
            ->with('success', 'Enquiry deleted successfully.');
    }
}

