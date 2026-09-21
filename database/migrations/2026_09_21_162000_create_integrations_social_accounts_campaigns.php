<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('integrations', function (Blueprint $table) {
            $table->id();
            $table->string('provider'); // buffer, meta, ga4, etc.
            $table->string('status')->default('disconnected'); // connected|disconnected|error|expired
            $table->string('external_account_name')->nullable();
            $table->json('scopes')->nullable();
            $table->text('access_token')->nullable();
            $table->text('refresh_token')->nullable();
            $table->timestamp('token_expires_at')->nullable();
            $table->timestamp('last_sync_at')->nullable();
            $table->text('last_error')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['provider', 'status']);
        });

        Schema::create('social_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('integration_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->string('platform'); // facebook, instagram, linkedin, etc.
            $table->string('name');
            $table->string('external_id')->nullable();
            $table->string('status')->default('active'); // active|inactive|error
            $table->string('token_status')->default('unknown'); // valid|expired|unknown|revoked
            $table->timestamp('token_expires_at')->nullable();
            $table->timestamp('last_sync_at')->nullable();
            $table->timestamp('last_posted_at')->nullable();
            $table->unsignedBigInteger('follower_count')->nullable();
            $table->boolean('can_publish')->default(true);
            $table->boolean('can_analytics')->default(true);
            $table->boolean('can_inbox')->default(false);
            $table->boolean('is_active')->default(true);
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['platform', 'is_active']);
            $table->index('product_id');
        });

        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('objective')->nullable();
            $table->foreignId('owner_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->string('status')->default('draft'); // draft|active|paused|completed|cancelled
            $table->text('audience_notes')->nullable();
            $table->decimal('budget_amount', 12, 2)->nullable();
            $table->string('utm_campaign')->nullable();
            $table->timestamps();

            $table->index('status');
        });

        Schema::create('campaign_product', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['campaign_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campaign_product');
        Schema::dropIfExists('campaigns');
        Schema::dropIfExists('social_accounts');
        Schema::dropIfExists('integrations');
    }
};
