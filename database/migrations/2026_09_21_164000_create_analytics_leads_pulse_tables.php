<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('metric_snapshots', function (Blueprint $table) {
            $table->id();
            $table->string('measurable_type');
            $table->unsignedBigInteger('measurable_id');
            $table->string('source'); // meta|buffer|ga4|internal
            $table->string('metric_key');
            $table->string('raw_key')->nullable();
            $table->decimal('value', 16, 4)->default(0);
            $table->timestamp('captured_at');
            $table->timestamps();

            $table->unique(
                ['measurable_type', 'measurable_id', 'source', 'metric_key', 'captured_at'],
                'metric_snapshots_unique'
            );
            $table->index(['measurable_type', 'measurable_id']);
        });

        Schema::create('demo_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contact_message_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('campaign_id')->nullable()->constrained()->nullOnDelete();
            $table->string('request_type')->default('demo');
            $table->timestamp('preferred_at')->nullable();
            $table->timestamp('scheduled_at')->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status')->default('requested');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('status');
        });

        Schema::create('marketing_alerts', function (Blueprint $table) {
            $table->id();
            $table->string('rule_key');
            $table->string('severity'); // warning|critical
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('social_account_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('campaign_id')->nullable()->constrained()->nullOnDelete();
            $table->string('status')->default('open'); // open|resolved|dismissed
            $table->string('title');
            $table->text('message')->nullable();
            $table->timestamp('triggered_at');
            $table->timestamp('resolved_at')->nullable();
            $table->json('payload')->nullable();
            $table->timestamps();

            $table->index(['status', 'severity']);
            $table->index('rule_key');
        });

        Schema::create('marketing_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignee_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('campaign_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type');
            $table->string('title');
            $table->text('notes')->nullable();
            $table->timestamp('due_at')->nullable();
            $table->string('status')->default('open'); // open|in_progress|done|cancelled
            $table->string('priority')->default('normal'); // low|normal|high|urgent
            $table->nullableMorphs('related');
            $table->timestamps();

            $table->index(['status', 'due_at']);
        });

        Schema::create('tracked_links', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('destination_url');
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('campaign_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('content_item_id')->nullable()->constrained()->nullOnDelete();
            $table->string('utm_source')->nullable();
            $table->string('utm_medium')->nullable();
            $table->string('utm_campaign')->nullable();
            $table->string('utm_content')->nullable();
            $table->string('utm_term')->nullable();
            $table->unsignedBigInteger('click_count')->default(0);
            $table->timestamps();
        });

        Schema::create('website_events', function (Blueprint $table) {
            $table->id();
            $table->string('event_name');
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('campaign_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('tracked_link_id')->nullable()->constrained()->nullOnDelete();
            $table->string('session_id')->nullable();
            $table->timestamp('occurred_at');
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['event_name', 'occurred_at']);
        });

        Schema::create('newsletter_subscribers', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->string('status')->default('subscribed');
            $table->string('product_interest')->nullable();
            $table->string('provider_id')->nullable();
            $table->timestamp('subscribed_at')->nullable();
            $table->timestamp('unsubscribed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('lead_communications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contact_message_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('channel')->default('email');
            $table->string('subject')->nullable();
            $table->text('body');
            $table->string('status')->default('sent');
            $table->timestamps();
        });

        // Migrate legacy enquiry statuses toward lead pipeline.
        DB::table('contact_messages')->where('status', 'read')->update(['status' => 'new']);
        DB::table('contact_messages')->where('status', 'responded')->update(['status' => 'contacted']);
    }

    public function down(): void
    {
        Schema::dropIfExists('lead_communications');
        Schema::dropIfExists('newsletter_subscribers');
        Schema::dropIfExists('website_events');
        Schema::dropIfExists('tracked_links');
        Schema::dropIfExists('marketing_tasks');
        Schema::dropIfExists('marketing_alerts');
        Schema::dropIfExists('demo_requests');
        Schema::dropIfExists('metric_snapshots');
    }
};
