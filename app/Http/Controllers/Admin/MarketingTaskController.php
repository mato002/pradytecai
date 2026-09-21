<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\MarketingTask;
use App\Models\Product;
use App\Models\User;
use App\Services\ActivityLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MarketingTaskController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()->can('tasks.view'), 403);

        $query = MarketingTask::query()
            ->with(['assignee', 'product', 'campaign'])
            ->latest();

        if ($productId = session('marketing.product_id')) {
            $query->where('product_id', $productId);
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $tasks = $query->paginate(30)->withQueryString();
        $user = $request->user();

        return view('admin.tasks.index', [
            'tasks' => $tasks,
            'products' => Product::query()->visibleTo($user)->ordered()->get(['id', 'name']),
            'campaigns' => Campaign::query()->visibleTo($user)->orderBy('name')->get(['id', 'name']),
            'assignees' => User::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless($request->user()->can('tasks.manage'), 403);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'type' => ['nullable', 'string', 'max:100'],
            'assignee_id' => ['nullable', 'integer', 'exists:users,id'],
            'product_id' => ['nullable', 'integer', 'exists:products,id'],
            'campaign_id' => ['nullable', 'integer', 'exists:campaigns,id'],
            'due_at' => ['nullable', 'date'],
            'priority' => ['nullable', 'string', 'in:low,normal,high,urgent'],
            'status' => ['nullable', 'string', 'in:open,in_progress,done,cancelled'],
        ]);

        $data['status'] = $data['status'] ?? 'open';
        $data['priority'] = $data['priority'] ?? 'normal';
        $data['type'] = $data['type'] ?? 'general';

        $task = MarketingTask::create($data);
        ActivityLogService::created($task, "Created task: {$task->title}");

        return redirect()
            ->route('admin.tasks.index')
            ->with('success', 'Task created.');
    }

    public function update(Request $request, MarketingTask $task): RedirectResponse
    {
        abort_unless($request->user()->can('tasks.manage'), 403);

        $data = $request->validate([
            'title' => ['sometimes', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'assignee_id' => ['nullable', 'integer', 'exists:users,id'],
            'due_at' => ['nullable', 'date'],
            'priority' => ['nullable', 'string', 'in:low,normal,high,urgent'],
            'status' => ['nullable', 'string', 'in:open,in_progress,done,cancelled'],
        ]);

        $old = $task->getAttributes();
        $task->update($data);
        ActivityLogService::updated($task, $old, "Updated task: {$task->title}");

        return redirect()
            ->route('admin.tasks.index')
            ->with('success', 'Task updated.');
    }

    public function destroy(MarketingTask $task): RedirectResponse
    {
        abort_unless(auth()->user()->can('tasks.manage'), 403);

        ActivityLogService::deleted($task, "Deleted task: {$task->title}");
        $task->delete();

        return redirect()
            ->route('admin.tasks.index')
            ->with('success', 'Task deleted.');
    }
}
