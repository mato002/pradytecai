<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityLogController extends Controller
{
    /**
     * Display a listing of activity logs
     */
    public function index(Request $request): View
    {
        $query = ActivityLog::with('user')->recent();

        // Filter by action if provided
        if ($request->has('action') && $request->action) {
            $query->where('action', $request->action);
        }

        // Filter by model type if provided
        if ($request->has('model_type') && $request->model_type) {
            $query->where('model_type', $request->model_type);
        }

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                    ->orWhere('action', 'like', "%{$search}%");
            });
        }

        $logs = $query->paginate(50);

        $actionCounts = [
            'all' => ActivityLog::count(),
            'created' => ActivityLog::where('action', 'created')->count(),
            'updated' => ActivityLog::where('action', 'updated')->count(),
            'deleted' => ActivityLog::where('action', 'deleted')->count(),
            'viewed' => ActivityLog::where('action', 'viewed')->count(),
        ];

        $modelTypes = ActivityLog::whereNotNull('model_type')
            ->distinct()
            ->pluck('model_type')
            ->map(function ($type) {
                return class_basename($type);
            })
            ->filter()
            ->unique()
            ->values();

        return view('admin.activity-logs.index', compact('logs', 'actionCounts', 'modelTypes'));
    }
}





