<?php

namespace Database\Seeders;

use App\Models\BlogPost;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Carbon\Carbon;

class BlogPostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $posts = [
            [
                'title' => 'New Features in BulkSMS CRM: Enhanced Analytics and Campaign Management',
                'slug' => 'new-features-bulksms-crm-enhanced-analytics',
                'category' => 'Product Updates',
                'excerpt' => 'We\'re excited to announce several new features in our BulkSMS CRM platform, including enhanced analytics, improved campaign management tools, and real-time delivery tracking.',
                'body' => 'We\'re thrilled to introduce major updates to BulkSMS CRM that will help you manage your multi-channel messaging campaigns more effectively. The new analytics dashboard provides real-time insights into message delivery rates, engagement metrics, and campaign performance across SMS, WhatsApp, and Email channels. Our improved campaign builder now supports advanced scheduling, A/B testing, and automated follow-ups. Additionally, we\'ve added real-time delivery tracking so you can monitor message status as it happens.',
                'is_published' => true,
                'published_at' => Carbon::now()->subDays(5),
            ],
            [
                'title' => 'The Future of Microfinance Technology: Digital Transformation in Emerging Markets',
                'slug' => 'future-microfinance-technology-digital-transformation',
                'category' => 'Industry Insights',
                'excerpt' => 'Exploring how technology is transforming the microfinance industry and what it means for financial inclusion in emerging markets across Africa and beyond.',
                'body' => 'The microfinance sector is experiencing a digital revolution that\'s making financial services more accessible to underserved communities. With platforms like Prady Mfi, microfinance institutions can now automate loan processing, manage client databases more efficiently, and generate comprehensive financial reports in real-time. This digital transformation is not just about efficiency—it\'s about expanding financial inclusion to millions who previously had limited access to banking services. We explore the trends, challenges, and opportunities in this rapidly evolving landscape.',
                'is_published' => true,
                'published_at' => Carbon::now()->subDays(10),
            ],
            [
                'title' => 'Best Practices for Multi-Channel Messaging: SMS, WhatsApp, and Email Integration',
                'slug' => 'best-practices-multi-channel-messaging-integration',
                'category' => 'Best Practices',
                'excerpt' => 'Learn how to effectively use SMS, WhatsApp, and Email together to maximize your communication reach and engagement rates with your audience.',
                'body' => 'Effective communication requires reaching your audience where they are most active. By integrating SMS, WhatsApp, and Email into a unified strategy, businesses can significantly improve their engagement rates. SMS is perfect for time-sensitive notifications and reminders. WhatsApp excels at two-way conversations and rich media sharing. Email remains the go-to channel for detailed information and formal communications. We share proven strategies for combining these channels, including when to use each platform, how to maintain consistent messaging, and best practices for avoiding message fatigue.',
                'is_published' => true,
                'published_at' => Carbon::now()->subDays(15),
            ],
            [
                'title' => 'Pradytec Year in Review 2024: Milestones, Growth, and Impact',
                'slug' => 'pradytec-year-review-2024-milestones-growth',
                'category' => 'Company News',
                'excerpt' => 'A comprehensive look back at our achievements, milestones, and the impact we\'ve made throughout 2024 in serving businesses across Kenya and beyond.',
                'body' => '2024 has been a transformative year for Pradytec. We\'ve expanded our client base significantly, with BulkSMS CRM now serving over 500 businesses and Prady Mfi supporting microfinance institutions across multiple countries. Our team has grown, our platform capabilities have expanded, and most importantly, we\'ve helped hundreds of organizations streamline their operations and improve their customer engagement. From launching new features to establishing strategic partnerships, this year has set a strong foundation for continued growth in 2025.',
                'is_published' => true,
                'published_at' => Carbon::now()->subDays(20),
            ],
            [
                'title' => 'API Integration Guide: Connecting Your Systems with Pradytec Platforms',
                'slug' => 'api-integration-guide-connecting-systems',
                'category' => 'Technical',
                'excerpt' => 'A comprehensive guide to integrating our APIs into your existing systems, with code examples, best practices, and troubleshooting tips.',
                'body' => 'Our RESTful APIs enable seamless integration between Pradytec platforms and your existing business systems. This guide covers everything from authentication and API keys to common integration patterns. We provide detailed code examples in multiple programming languages, explain rate limiting and error handling, and share best practices for maintaining secure, reliable integrations. Whether you\'re connecting your CRM, ERP, or custom applications, this guide will help you get started quickly and avoid common pitfalls.',
                'is_published' => true,
                'published_at' => Carbon::now()->subDays(25),
            ],
            [
                'title' => 'Enterprise Security Best Practices: Protecting Your Data and Customer Information',
                'slug' => 'enterprise-security-best-practices-data-protection',
                'category' => 'Security',
                'excerpt' => 'How we ensure your data is secure and what you can do to maintain security best practices in your organization when using enterprise software solutions.',
                'body' => 'Security is paramount when handling sensitive business and customer data. At Pradytec, we implement multiple layers of security including encryption at rest and in transit, regular security audits, and compliance with international data protection standards. This article outlines the security measures we\'ve built into our platforms and provides actionable recommendations for organizations to enhance their own security posture. Topics covered include access control, password policies, data backup strategies, and incident response planning.',
                'is_published' => true,
                'published_at' => Carbon::now()->subDays(30),
            ],
            [
                'title' => 'How BulkSMS CRM Helped a Kenyan Retail Chain Increase Customer Engagement by 300%',
                'slug' => 'bulksms-crm-success-story-retail-chain-engagement',
                'category' => 'Case Studies',
                'excerpt' => 'A real-world success story of how a major retail chain in Kenya leveraged BulkSMS CRM to transform their customer communication strategy and dramatically increase engagement.',
                'body' => 'This case study details how a leading retail chain with over 50 locations across Kenya implemented BulkSMS CRM to streamline their customer communications. By integrating SMS, WhatsApp, and Email campaigns, they were able to send personalized promotions, order confirmations, and delivery updates. The results were remarkable: a 300% increase in customer engagement, 45% improvement in repeat purchase rates, and significant reduction in customer service inquiries. We break down their implementation strategy, the challenges they faced, and the key factors that contributed to their success.',
                'is_published' => true,
                'published_at' => Carbon::now()->subDays(35),
            ],
            [
                'title' => 'Understanding Loan Management in Microfinance: A Guide to Prady Mfi Features',
                'slug' => 'loan-management-microfinance-prady-mfi-guide',
                'category' => 'Product Guides',
                'excerpt' => 'An in-depth guide to using Prady Mfi\'s loan management features, from application processing to repayment tracking and financial reporting.',
                'body' => 'Prady Mfi offers comprehensive loan management capabilities designed specifically for microfinance institutions. This guide walks you through the entire loan lifecycle: from initial client application and credit assessment to loan disbursement, repayment tracking, and generating financial reports. We cover advanced features like automated payment reminders, risk assessment tools, and compliance reporting. Whether you\'re new to digital loan management or looking to optimize your existing processes, this guide provides practical insights and step-by-step instructions.',
                'is_published' => true,
                'published_at' => Carbon::now()->subDays(40),
            ],
            [
                'title' => 'The Role of Technology in Financial Inclusion: Pradytec\'s Mission',
                'slug' => 'technology-financial-inclusion-pradytec-mission',
                'category' => 'Company News',
                'excerpt' => 'How Pradytec is contributing to financial inclusion across Africa by providing accessible, affordable technology solutions for microfinance institutions and small businesses.',
                'body' => 'Financial inclusion remains one of the most critical challenges in emerging markets. At Pradytec, we believe technology can be a powerful enabler of financial access. Through platforms like Prady Mfi, we\'re helping microfinance institutions reach more clients, process loans faster, and operate more efficiently. This article explores our mission, the impact we\'ve seen so far, and our vision for the future. We discuss how affordable technology solutions can help bridge the gap between traditional banking and underserved communities, and share stories from institutions that have transformed their operations using our platforms.',
                'is_published' => true,
                'published_at' => Carbon::now()->subDays(45),
            ],
            [
                'title' => 'Getting Started with BulkSMS CRM: A Step-by-Step Setup Guide',
                'slug' => 'getting-started-bulksms-crm-setup-guide',
                'category' => 'Product Guides',
                'excerpt' => 'New to BulkSMS CRM? This comprehensive setup guide will walk you through account creation, initial configuration, and sending your first campaign.',
                'body' => 'Setting up BulkSMS CRM is straightforward, but following best practices from the start will help you get the most out of the platform. This guide covers everything from creating your account and verifying your channels to importing contacts, creating your first campaign, and understanding the analytics dashboard. We include tips for optimizing your message delivery rates, managing your wallet balance, and setting up automated workflows. By the end of this guide, you\'ll be ready to launch your first multi-channel messaging campaign.',
                'is_published' => true,
                'published_at' => Carbon::now()->subDays(50),
            ],
        ];

        foreach ($posts as $post) {
            BlogPost::create($post);
        }
    }
}



