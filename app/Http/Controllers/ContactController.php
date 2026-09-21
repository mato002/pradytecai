<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use App\Models\DemoRequest;
use App\Models\Product;
use App\Models\WebsiteEvent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'company' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'topic' => ['nullable', 'string', 'max:255'],
            'product_id' => ['nullable', 'integer', 'exists:products,id'],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string'],
            'request_type' => ['nullable', 'string', 'in:enquiry,demo,support'],
            'source' => ['nullable', 'string', 'max:255'],
            'referrer' => ['nullable', 'string', 'max:500'],
            'landing_page' => ['nullable', 'string', 'max:500'],
            'preferred_at' => ['nullable', 'date'],
            'utm_source' => ['nullable', 'string', 'max:255'],
            'utm_medium' => ['nullable', 'string', 'max:255'],
            'utm_campaign' => ['nullable', 'string', 'max:255'],
            'utm_content' => ['nullable', 'string', 'max:255'],
            'utm_term' => ['nullable', 'string', 'max:255'],
        ]);

        $product = $this->resolveProduct($request, $data);

        $requestType = $data['request_type']
            ?? (str_starts_with(strtolower($data['subject']), 'demo') ? 'demo' : 'enquiry');

        $utm = $this->resolveUtms($request, $data);

        $lead = ContactMessage::create([
            'name' => $data['name'],
            'company' => $data['company'] ?? null,
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'topic' => $data['topic'] ?? $product?->slug,
            'subject' => $data['subject'],
            'message' => $data['message'],
            'status' => 'new',
            'product_id' => $product?->id,
            'request_type' => $requestType,
            'source' => $data['source'] ?? $request->input('source', 'website'),
            'referrer' => $data['referrer'] ?? $request->headers->get('referer'),
            'landing_page' => $data['landing_page']
                ?? $request->input('landing_page')
                ?? $request->headers->get('referer'),
            'utm_source' => $utm['utm_source'],
            'utm_medium' => $utm['utm_medium'],
            'utm_campaign' => $utm['utm_campaign'],
            'utm_content' => $utm['utm_content'],
            'utm_term' => $utm['utm_term'],
        ]);

        if ($requestType === 'demo') {
            DemoRequest::create([
                'contact_message_id' => $lead->id,
                'product_id' => $product?->id,
                'request_type' => 'demo',
                'preferred_at' => $data['preferred_at'] ?? null,
                'status' => 'requested',
            ]);
        }

        WebsiteEvent::create([
            'event_name' => $requestType === 'demo' ? 'demo_submit' : 'enquiry_submit',
            'product_id' => $product?->id,
            'session_id' => $request->session()->getId(),
            'occurred_at' => now(),
            'meta' => ['contact_message_id' => $lead->id],
        ]);

        return back()->with('success', 'Thank you, your message has been received. Our team will contact you shortly.');
    }

    private function resolveProduct(Request $request, array $data): ?Product
    {
        if (! empty($data['product_id'])) {
            return Product::find($data['product_id']);
        }

        $key = $data['topic']
            ?? $request->query('product')
            ?? $request->input('product')
            ?? $request->query('topic');

        if (! $key) {
            return null;
        }

        if (is_numeric($key)) {
            return Product::find((int) $key);
        }

        return Product::query()
            ->where('slug', $key)
            ->orWhere('name', $key)
            ->orWhere('code', $key)
            ->first();
    }

    /**
     * @return array{utm_source: ?string, utm_medium: ?string, utm_campaign: ?string, utm_content: ?string, utm_term: ?string}
     */
    private function resolveUtms(Request $request, array $data): array
    {
        $keys = ['utm_source', 'utm_medium', 'utm_campaign', 'utm_content', 'utm_term'];
        $out = [];

        foreach ($keys as $key) {
            $out[$key] = $data[$key]
                ?? $request->query($key)
                ?? $request->session()->get('utm.'.$key)
                ?? null;
        }

        return $out;
    }
}
