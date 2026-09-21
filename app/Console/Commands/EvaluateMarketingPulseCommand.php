<?php

namespace App\Console\Commands;

use App\Models\Campaign;
use App\Models\ContactMessage;
use App\Models\ContentDestination;
use App\Models\ContentItem;
use App\Models\MarketingAlert;
use App\Models\Product;
use App\Models\SocialAccount;
use Illuminate\Console\Command;

class EvaluateMarketingPulseCommand extends Command
{
    protected $signature = 'marketing:pulse';

    protected $description = 'Evaluate marketing pulse rules and upsert open alerts idempotently';

    public function handle(): int
    {
        $this->evaluateSocialAccountInactive();
        $this->evaluateProductNotMarketed();
        $this->evaluateNoContentScheduledNextWeek();
        $this->evaluateDestinationFailed();
        $this->evaluateTokenExpired();
        $this->evaluateLeadFollowUpOverdue();
        $this->evaluateCampaignWithoutContent();

        $this->info('Marketing pulse evaluation complete.');

        return self::SUCCESS;
    }

    private function evaluateSocialAccountInactive(): void
    {
        $rule = 'social_account_inactive';
        $activeKeys = [];

        $accounts = SocialAccount::query()
            ->active()
            ->where(function ($q) {
                $q->whereNull('last_posted_at')
                    ->orWhere('last_posted_at', '<', now()->subDays(5));
            })
            ->get();

        foreach ($accounts as $account) {
            $fingerprint = $rule.':account:'.$account->id;
            $activeKeys[] = $fingerprint;
            $this->upsertAlert($rule, 'warning', $fingerprint, [
                'title' => 'Social account inactive',
                'message' => "{$account->name} has not posted in 5+ days.",
                'product_id' => $account->product_id,
                'social_account_id' => $account->id,
                'payload' => ['social_account_id' => $account->id],
            ]);
        }

        $this->resolveStale($rule, $activeKeys);
    }

    private function evaluateProductNotMarketed(): void
    {
        $rule = 'product_not_marketed';
        $activeKeys = [];

        $products = Product::query()
            ->active()
            ->where(function ($q) {
                $q->whereNull('last_marketed_at')
                    ->orWhere('last_marketed_at', '<', now()->subDays(14));
            })
            ->get();

        foreach ($products as $product) {
            $fingerprint = $rule.':product:'.$product->id;
            $activeKeys[] = $fingerprint;
            $this->upsertAlert($rule, 'warning', $fingerprint, [
                'title' => 'Product not marketed',
                'message' => "{$product->name} has no marketing activity in 14+ days.",
                'product_id' => $product->id,
                'payload' => ['product_id' => $product->id],
            ]);
        }

        $this->resolveStale($rule, $activeKeys);
    }

    private function evaluateNoContentScheduledNextWeek(): void
    {
        $rule = 'no_content_scheduled_next_week';
        $activeKeys = [];

        $hasScheduled = ContentItem::query()
            ->whereNotNull('scheduled_at')
            ->whereBetween('scheduled_at', [now(), now()->addDays(7)])
            ->exists();

        if (! $hasScheduled) {
            $fingerprint = $rule.':global';
            $activeKeys[] = $fingerprint;
            $this->upsertAlert($rule, 'warning', $fingerprint, [
                'title' => 'No content scheduled',
                'message' => 'There is no content scheduled for the next 7 days.',
                'payload' => [],
            ]);
        }

        $this->resolveStale($rule, $activeKeys);
    }

    private function evaluateDestinationFailed(): void
    {
        $rule = 'destination_failed';
        $activeKeys = [];

        $failed = ContentDestination::query()
            ->where('status', 'failed')
            ->with('contentItem')
            ->get();

        foreach ($failed as $destination) {
            $fingerprint = $rule.':destination:'.$destination->id;
            $activeKeys[] = $fingerprint;
            $this->upsertAlert($rule, 'critical', $fingerprint, [
                'title' => 'Destination publish failed',
                'message' => $destination->failure_message
                    ?: 'A content destination failed to publish.',
                'product_id' => $destination->contentItem?->product_id,
                'social_account_id' => $destination->social_account_id,
                'payload' => [
                    'content_destination_id' => $destination->id,
                    'content_item_id' => $destination->content_item_id,
                ],
            ]);
        }

        $this->resolveStale($rule, $activeKeys);
    }

    private function evaluateTokenExpired(): void
    {
        $rule = 'token_expired';
        $activeKeys = [];

        $accounts = SocialAccount::query()
            ->where(function ($q) {
                $q->where('token_status', 'expired')
                    ->orWhere(function ($inner) {
                        $inner->whereNotNull('token_expires_at')
                            ->where('token_expires_at', '<=', now());
                    });
            })
            ->get();

        foreach ($accounts as $account) {
            $fingerprint = $rule.':account:'.$account->id;
            $activeKeys[] = $fingerprint;
            $this->upsertAlert($rule, 'critical', $fingerprint, [
                'title' => 'Social token expired',
                'message' => "Token for {$account->name} is expired or invalid.",
                'product_id' => $account->product_id,
                'social_account_id' => $account->id,
                'payload' => ['social_account_id' => $account->id],
            ]);
        }

        $this->resolveStale($rule, $activeKeys);
    }

    private function evaluateLeadFollowUpOverdue(): void
    {
        $rule = 'lead_follow_up_overdue';
        $activeKeys = [];

        $leads = ContactMessage::query()->awaitingFollowUp()->get();

        foreach ($leads as $lead) {
            $fingerprint = $rule.':lead:'.$lead->id;
            $activeKeys[] = $fingerprint;
            $this->upsertAlert($rule, 'warning', $fingerprint, [
                'title' => 'Lead follow-up overdue',
                'message' => "Follow-up overdue for {$lead->name} ({$lead->email}).",
                'product_id' => $lead->product_id,
                'payload' => ['contact_message_id' => $lead->id],
            ]);
        }

        $this->resolveStale($rule, $activeKeys);
    }

    private function evaluateCampaignWithoutContent(): void
    {
        $rule = 'campaign_without_content';
        $activeKeys = [];

        $campaigns = Campaign::query()
            ->active()
            ->whereDoesntHave('contentItems')
            ->get();

        foreach ($campaigns as $campaign) {
            $fingerprint = $rule.':campaign:'.$campaign->id;
            $activeKeys[] = $fingerprint;
            $this->upsertAlert($rule, 'warning', $fingerprint, [
                'title' => 'Campaign without content',
                'message' => "Active campaign \"{$campaign->name}\" has no content items.",
                'campaign_id' => $campaign->id,
                'payload' => ['campaign_id' => $campaign->id],
            ]);
        }

        $this->resolveStale($rule, $activeKeys);
    }

    /**
     * @param  array<string, mixed>  $attrs
     */
    private function upsertAlert(string $ruleKey, string $severity, string $fingerprint, array $attrs): void
    {
        $payload = array_merge($attrs['payload'] ?? [], ['_fingerprint' => $fingerprint]);

        $existing = MarketingAlert::query()
            ->where('rule_key', $ruleKey)
            ->where('status', 'open')
            ->where('payload->_fingerprint', $fingerprint)
            ->first();

        if ($existing) {
            $existing->update([
                'severity' => $severity,
                'title' => $attrs['title'],
                'message' => $attrs['message'],
                'product_id' => $attrs['product_id'] ?? null,
                'social_account_id' => $attrs['social_account_id'] ?? null,
                'campaign_id' => $attrs['campaign_id'] ?? null,
                'payload' => $payload,
            ]);

            return;
        }

        MarketingAlert::create([
            'rule_key' => $ruleKey,
            'severity' => $severity,
            'product_id' => $attrs['product_id'] ?? null,
            'social_account_id' => $attrs['social_account_id'] ?? null,
            'campaign_id' => $attrs['campaign_id'] ?? null,
            'status' => 'open',
            'title' => $attrs['title'],
            'message' => $attrs['message'],
            'triggered_at' => now(),
            'payload' => $payload,
        ]);
    }

    /**
     * @param  list<string>  $activeFingerprints
     */
    private function resolveStale(string $ruleKey, array $activeFingerprints): void
    {
        $open = MarketingAlert::query()
            ->where('rule_key', $ruleKey)
            ->where('status', 'open')
            ->get();

        foreach ($open as $alert) {
            $fp = $alert->payload['_fingerprint'] ?? null;
            if ($fp === null || ! in_array($fp, $activeFingerprints, true)) {
                $alert->update([
                    'status' => 'resolved',
                    'resolved_at' => now(),
                ]);
            }
        }
    }
}
