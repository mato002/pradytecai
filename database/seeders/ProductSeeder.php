<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $portfolio = config('portfolio.products', []);

        $order = 1;
        foreach ($portfolio as $item) {
            Product::updateOrCreate(
                ['slug' => $item['slug']],
                [
                    'name' => $item['name'],
                    'short' => $item['short'] ?? null,
                    'description' => $item['description'] ?? $item['short'] ?? null,
                    'market' => $item['market'] ?? null,
                    'icon' => $item['icon'] ?? null,
                    'type' => $item['market'] ?? 'Product',
                    'url' => '/contact?product='.urlencode($item['slug']),
                    'button_text' => 'Request Demo',
                    'is_active' => true,
                    'order' => $order++,
                    'features' => [],
                    'benefits' => [],
                    'statistics' => [],
                ]
            );
        }

        // Keep BulkSMS CRM as a real Pradytec product if sold; unpublish GSC.
        Product::updateOrCreate(
            ['slug' => 'bulksms-crm'],
            [
                'name' => 'BulkSMS CRM',
                'short' => 'Multi-channel messaging platform for SMS, WhatsApp and Email.',
                'description' => 'Multi-channel messaging (SMS, WhatsApp, Email) with contacts, campaigns and analytics.',
                'market' => 'Businesses needing customer messaging',
                'type' => 'Messaging Platform',
                'url' => 'https://crm.pradytecai.com',
                'button_text' => 'Open BulkSMS CRM',
                'icon' => 'messaging',
                'is_active' => true,
                'order' => $order++,
                'features' => [
                    'Multi-channel messaging (SMS, WhatsApp, Email)',
                    'Contact and segment management',
                    'Campaign builder with scheduling and automation',
                    'Real-time delivery and engagement analytics',
                ],
                'benefits' => [
                    'Reach customers on their preferred channels',
                    'Improve campaign performance with detailed insights',
                    'Automate routine notifications and reminders',
                    'Scale communications without extra headcount',
                ],
                'statistics' => ['99.9%', 'Typical delivery rate', '3+', 'Supported messaging channels'],
            ]
        );

        // Deprecate seeded Google Search Console “product”.
        Product::where('name', 'Google Search Console')
            ->orWhere('slug', 'google-search-console')
            ->update(['is_active' => false, 'slug' => 'google-search-console']);

        // Normalize any legacy Prady Mfi row onto portfolio microfinance slug if duplicate name.
        $legacyMfi = Product::where('name', 'Prady Mfi')->where(function ($q) {
            $q->whereNull('slug')->orWhere('slug', '!=', 'prady-microfinance');
        })->first();

        if ($legacyMfi) {
            $canonical = Product::where('slug', 'prady-microfinance')->first();
            if ($canonical) {
                $legacyMfi->update(['is_active' => false, 'slug' => 'prady-mfi-legacy']);
            } else {
                $legacyMfi->update([
                    'slug' => 'prady-microfinance',
                    'name' => 'Prady Microfinance',
                    'is_active' => true,
                ]);
            }
        }

        // Ensure any product missing slug gets one.
        Product::whereNull('slug')->orWhere('slug', '')->each(function (Product $product) {
            $product->update(['slug' => Str::slug($product->name)]);
        });
    }
}
