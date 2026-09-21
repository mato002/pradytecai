<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class SocialAccount extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'integration_id',
        'product_id',
        'platform',
        'name',
        'external_id',
        'status',
        'token_status',
        'token_expires_at',
        'last_sync_at',
        'last_posted_at',
        'follower_count',
        'can_publish',
        'can_analytics',
        'can_inbox',
        'is_active',
        'meta',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'token_expires_at' => 'datetime',
            'last_sync_at' => 'datetime',
            'last_posted_at' => 'datetime',
            'follower_count' => 'integer',
            'can_publish' => 'boolean',
            'can_analytics' => 'boolean',
            'can_inbox' => 'boolean',
            'is_active' => 'boolean',
            'meta' => 'array',
        ];
    }

    public function integration(): BelongsTo
    {
        return $this->belongsTo(Integration::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function contentDestinations(): HasMany
    {
        return $this->hasMany(ContentDestination::class);
    }

    public function metricSnapshots(): MorphMany
    {
        return $this->morphMany(MetricSnapshot::class, 'measurable');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        if ($user->hasUnrestrictedScope()) {
            return $query;
        }

        $productIds = $user->scopedProductIds();
        if ($productIds !== null) {
            $query->where(function (Builder $inner) use ($productIds) {
                $inner->whereNull('product_id')
                    ->orWhereIn('product_id', $productIds);
            });
        }

        $accountIds = $user->accessScopes
            ->where('scope_type', UserAccessScope::TYPE_SOCIAL_ACCOUNT)
            ->pluck('scope_id')
            ->map(fn ($id) => (int) $id)
            ->values()
            ->all();

        if ($accountIds !== []) {
            $query->whereIn('id', $accountIds);
        }

        return $query;
    }
}
