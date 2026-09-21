<?php

namespace App\Http\Controllers;

use App\Models\NewsletterSubscriber;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    public function subscribe(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'product_interest' => ['nullable', 'string', 'max:255'],
        ]);

        $subscriber = NewsletterSubscriber::query()->firstOrNew(['email' => $data['email']]);

        $subscriber->fill([
            'status' => 'subscribed',
            'product_interest' => $data['product_interest'] ?? $subscriber->product_interest,
            'subscribed_at' => $subscriber->subscribed_at ?? now(),
            'unsubscribed_at' => null,
        ]);
        $subscriber->save();

        return redirect()
            ->back()
            ->with('newsletter_success', 'Thank you for subscribing!');
    }
}
