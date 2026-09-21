<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubscriberController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()->can('subscribers.view'), 403);

        $query = NewsletterSubscriber::query()->latest('subscribed_at');

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where('email', 'like', '%'.$request->search.'%');
        }

        $subscribers = $query->paginate(30)->withQueryString();

        return view('admin.subscribers.index', compact('subscribers'));
    }
}
