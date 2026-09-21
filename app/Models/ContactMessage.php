<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ContactMessage extends Model
{
    use HasFactory;

    public const LEAD_STATUSES = [
        'new',
        'contacted',
        'qualified',
        'demo_booked',
        'demo_completed',
        'proposal',
        'won',
        'lost',
        'unqualified',
        'archived',
    ];

    /**
     * Terminal / closed lead statuses (no longer awaiting follow-up).
     *
     * @var list<string>
     */
    public const CLOSED_LEAD_STATUSES = [
        'won',
        'lost',
        'unqualified',
        'archived',
    ];

    /**
     * @var list<string>
     */
    protected $fillable = [
        'product_id',
        'campaign_id',
        'content_destination_id',
        'name',
        'company',
        'email',
        'phone',
        'topic',
        'request_type',
        'source',
        'referrer',
        'landing_page',
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'utm_content',
        'utm_term',
        'next_follow_up_at',
        'first_responded_at',
        'lead_value',
        'subject',
        'message',
        'status',
        'admin_notes',
        'read_at',
        'responded_at',
        'read_by',
        'responded_by',
        'assigned_to',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'read_at' => 'datetime',
            'responded_at' => 'datetime',
            'next_follow_up_at' => 'datetime',
            'first_responded_at' => 'datetime',
            'lead_value' => 'decimal:2',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function contentDestination(): BelongsTo
    {
        return $this->belongsTo(ContentDestination::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function readBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'read_by');
    }

    public function respondedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responded_by');
    }

    public function demoRequests(): HasMany
    {
        return $this->hasMany(DemoRequest::class);
    }

    public function communications(): HasMany
    {
        return $this->hasMany(LeadCommunication::class);
    }

    public function scopeUnread(Builder $query): Builder
    {
        return $query->whereNull('read_at');
    }

    public function scopeNew(Builder $query): Builder
    {
        return $query->where('status', 'new');
    }

    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        $productIds = $user->scopedProductIds();

        if ($productIds === null) {
            return $query;
        }

        return $query->where(function (Builder $inner) use ($productIds) {
            $inner->whereNull('product_id')
                ->orWhereIn('product_id', $productIds);
        });
    }

    public function scopeAwaitingFollowUp(Builder $query): Builder
    {
        return $query
            ->whereNotNull('next_follow_up_at')
            ->where('next_follow_up_at', '<=', now())
            ->whereNotIn('status', self::CLOSED_LEAD_STATUSES);
    }

    public function isUnread(): bool
    {
        return is_null($this->read_at);
    }

    public function isResponded(): bool
    {
        return ! is_null($this->responded_at);
    }
}
