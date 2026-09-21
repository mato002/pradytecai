<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('slug')->nullable()->unique()->after('name');
            $table->string('short')->nullable()->after('description');
            $table->string('market')->nullable()->after('short');
            $table->string('code')->nullable()->after('market');
            $table->timestamp('last_marketed_at')->nullable()->after('order');
        });

        Schema::table('contact_messages', function (Blueprint $table) {
            $table->unsignedBigInteger('product_id')->nullable()->after('id');
            $table->unsignedBigInteger('campaign_id')->nullable()->after('product_id');
            $table->unsignedBigInteger('content_destination_id')->nullable()->after('campaign_id');
            $table->unsignedBigInteger('assigned_to')->nullable()->after('responded_by');
            $table->string('request_type')->nullable()->after('topic');
            $table->string('source')->nullable()->after('request_type');
            $table->string('referrer')->nullable()->after('source');
            $table->string('landing_page')->nullable()->after('referrer');
            $table->string('utm_source')->nullable()->after('landing_page');
            $table->string('utm_medium')->nullable()->after('utm_source');
            $table->string('utm_campaign')->nullable()->after('utm_medium');
            $table->string('utm_content')->nullable()->after('utm_campaign');
            $table->string('utm_term')->nullable()->after('utm_content');
            $table->timestamp('next_follow_up_at')->nullable()->after('utm_term');
            $table->timestamp('first_responded_at')->nullable()->after('next_follow_up_at');
            $table->decimal('lead_value', 12, 2)->nullable()->after('first_responded_at');

            $table->index('product_id');
            $table->index('campaign_id');
            $table->index('status');
            $table->index('assigned_to');
        });
    }

    public function down(): void
    {
        Schema::table('contact_messages', function (Blueprint $table) {
            $table->dropColumn([
                'product_id', 'campaign_id', 'content_destination_id', 'assigned_to', 'request_type',
                'source', 'referrer', 'landing_page', 'utm_source', 'utm_medium', 'utm_campaign',
                'utm_content', 'utm_term', 'next_follow_up_at', 'first_responded_at', 'lead_value',
            ]);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['slug', 'short', 'market', 'code', 'last_marketed_at']);
        });
    }
};
