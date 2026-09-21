<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Models\LeadCommunication;
use App\Models\Product;
use App\Models\User;
use App\Services\ActivityLogService;
use App\Services\CommunicationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EnquiryController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', ContactMessage::class);

        $user = $request->user();
        $query = ContactMessage::query()
            ->with(['product', 'assignee'])
            ->visibleTo($user)
            ->latest();

        if ($productId = session('marketing.product_id') ?: $request->input('product_id')) {
            $query->where('product_id', $productId);
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('assigned_to')) {
            $query->where('assigned_to', $request->assigned_to);
        }

        if ($request->filled('next_follow_up_at')) {
            $query->whereDate('next_follow_up_at', $request->next_follow_up_at);
        } elseif ($request->boolean('awaiting_follow_up')) {
            $query->awaitingFollowUp();
        }

        if ($request->filled('topic')) {
            $query->where('topic', $request->topic);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('subject', 'like', "%{$search}%")
                    ->orWhere('message', 'like', "%{$search}%");
            });
        }

        if ($request->has('export') && $request->export === 'csv') {
            abort_unless($user->can('leads.export'), 403);

            return $this->exportCsv($query->get());
        }

        $messages = $query->paginate(20)->withQueryString();

        $baseCounts = ContactMessage::query()->visibleTo($user);
        if ($sessionProduct = session('marketing.product_id')) {
            $baseCounts->where('product_id', $sessionProduct);
        }

        $statusCounts = ['all' => (clone $baseCounts)->count()];
        foreach (ContactMessage::LEAD_STATUSES as $status) {
            $statusCounts[$status] = (clone $baseCounts)->where('status', $status)->count();
        }

        $topics = ContactMessage::query()
            ->visibleTo($user)
            ->whereNotNull('topic')
            ->distinct()
            ->pluck('topic')
            ->filter()
            ->values();

        $assignees = User::orderBy('name')->get(['id', 'name']);
        $products = Product::query()->visibleTo($user)->ordered()->get(['id', 'name']);
        $leadStatuses = ContactMessage::LEAD_STATUSES;

        return view('admin.enquiries.index', compact(
            'messages',
            'statusCounts',
            'topics',
            'assignees',
            'products',
            'leadStatuses'
        ));
    }

    private function exportCsv($leads)
    {
        $filename = 'leads_'.date('Y-m-d_His').'.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($leads) {
            $file = fopen('php://output', 'w');

            fputcsv($file, [
                'ID', 'Name', 'Company', 'Email', 'Phone', 'Topic', 'Product', 'Subject',
                'Message', 'Status', 'Assigned To', 'Next Follow Up', 'Received At', 'First Responded At',
            ]);

            foreach ($leads as $lead) {
                fputcsv($file, [
                    $lead->id,
                    $lead->name,
                    $lead->company ?? '',
                    $lead->email,
                    $lead->phone ?? '',
                    $lead->topic ?? '',
                    $lead->product?->name ?? '',
                    $lead->subject,
                    $lead->message,
                    $lead->status,
                    $lead->assignee?->name ?? '',
                    $lead->next_follow_up_at?->format('Y-m-d H:i:s'),
                    $lead->created_at?->format('Y-m-d H:i:s'),
                    $lead->first_responded_at?->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function show(ContactMessage $enquiry): View
    {
        $this->authorize('view', $enquiry);

        if (! $enquiry->read_at) {
            $enquiry->update([
                'read_at' => now(),
                'read_by' => auth()->id(),
            ]);
            ActivityLogService::viewed($enquiry, "Viewed lead from {$enquiry->name}");
        }

        $enquiry->load(['readBy', 'respondedBy', 'assignee', 'product', 'communications.user']);

        return view('admin.enquiries.show', [
            'enquiry' => $enquiry,
            'leadStatuses' => ContactMessage::LEAD_STATUSES,
            'statuses' => ContactMessage::LEAD_STATUSES,
            'assignees' => User::orderBy('name')->get(['id', 'name']),
            'users' => User::orderBy('name')->get(['id', 'name']),
            'products' => Product::query()->visibleTo(auth()->user())->ordered()->get(['id', 'name']),
        ]);
    }

    public function updateStatus(Request $request, ContactMessage $enquiry): RedirectResponse
    {
        $this->authorize('update', $enquiry);

        $validated = $request->validate([
            'status' => 'required|in:'.implode(',', ContactMessage::LEAD_STATUSES),
            'admin_notes' => 'nullable|string',
            'assigned_to' => 'nullable|integer|exists:users,id',
            'next_follow_up_at' => 'nullable|date',
            'lead_value' => 'nullable|numeric|min:0',
            'product_id' => 'nullable|integer|exists:products,id',
        ]);

        if (isset($validated['assigned_to'])) {
            $this->authorize('assign', $enquiry);
        }

        $oldStatus = $enquiry->status;
        $enquiry->update($validated);

        ActivityLogService::updated(
            $enquiry,
            ['status' => $oldStatus],
            "Updated lead status from {$oldStatus} to {$validated['status']}"
        );

        return redirect()->route('admin.enquiries.show', $enquiry)
            ->with('success', 'Lead updated successfully.');
    }

    public function reply(Request $request, ContactMessage $enquiry): RedirectResponse
    {
        abort_unless($request->user()->can('inbox.reply') || $request->user()->can('leads.manage'), 403);
        $this->authorize('view', $enquiry);

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

            $update = [
                'status' => 'contacted',
                'responded_at' => now(),
                'responded_by' => auth()->id(),
            ];

            if (! $enquiry->first_responded_at) {
                $update['first_responded_at'] = now();
            }

            $enquiry->update($update);

            LeadCommunication::create([
                'contact_message_id' => $enquiry->id,
                'user_id' => auth()->id(),
                'channel' => 'email',
                'subject' => $validated['reply_subject'],
                'body' => $validated['reply_message'],
                'status' => 'sent',
            ]);

            ActivityLogService::custom('replied', $enquiry, "Replied to lead from {$enquiry->name}");

            return redirect()->route('admin.enquiries.show', $enquiry)
                ->with('success', 'Reply sent successfully.');
        } catch (\Exception $e) {
            return redirect()->route('admin.enquiries.show', $enquiry)
                ->with('error', 'Failed to send reply: '.$e->getMessage());
        }
    }

    public function destroy(Request $request, ContactMessage $enquiry): RedirectResponse
    {
        $this->authorize('delete', $enquiry);

        $enquiryName = $enquiry->name;
        ActivityLogService::deleted($enquiry, "Deleted lead from {$enquiryName}");

        $enquiry->delete();

        return redirect()->route('admin.enquiries.index')
            ->with('success', 'Lead deleted successfully.');
    }
}
