<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Seed or update the core products with richer details
        $products = [
            [
                'name' => 'BulkSMS CRM',
                'type' => 'Messaging Platform',
                'url' => 'https://crm.pradytecai.com',
                'button_text' => 'Open BulkSMS CRM',
                'icon' => 'messaging',
                'is_active' => true,
                'order' => 1,
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
                'statistics' => [
                    '99.9%', 'Typical delivery rate',
                    '3+',   'Supported messaging channels',
                ],
            ],
            [
                'name' => 'Google Search Console',
                'type' => 'Webmaster Tool',
                'url' => 'https://search.google.com/search-console',
                'button_text' => 'Open Search Console',
                'icon' => 'cloud',
                'is_active' => true,
                'order' => 3,
                'features' => [
                    'Monitor site indexing and search performance',
                    'View search queries and top pages',
                    'Identify crawling and mobile usability issues',
                    'Submit sitemaps and individual URLs',
                ],
                'benefits' => [
                    'Understand how Google sees your website',
                    'Fix SEO issues before they impact traffic',
                    'Track impressions, clicks and average position',
                    'Improve overall search visibility over time',
                ],
                'statistics' => [
                    '100%', 'Google search coverage overview',
                    '24/7', 'Monitoring and reporting',
                ],
            ],
            [
                'name' => 'Prady Mfi',
                'type' => 'Microfinance System',
                'url' => 'https://demo.pradytecai.com',
                'button_text' => 'Open Prady Mfi Demo',
                'icon' => 'finance',
                'is_active' => true,
                'order' => 2,
                'features' => [
                    'End-to-end loan lifecycle management',
                    'Client onboarding and KYC records',
                    'Repayment tracking and arrears management',
                    'Comprehensive financial and portfolio reports',
                ],
                'benefits' => [
                    'Reduce manual paperwork and spreadsheet errors',
                    'Gain real-time visibility into portfolio health',
                    'Standardise processes across branches',
                    'Support regulatory and compliance reporting',
                ],
                'statistics' => [
                    '10,000+', 'Loans managed for institutions',
                    '5+',      'Countries supported',
                ],
            ],
        ];

        foreach ($products as $data) {
            Product::updateOrCreate(
                ['name' => $data['name']],
                $data
            );
        }
    }
}


