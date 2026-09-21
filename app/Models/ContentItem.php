<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ContentItem extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'product_id',
        'campaign_id',
        'author_id',
        'approver_id',
        'status',
        'title',
        'caption',
        'body',
        'cta_label',
        'destination_url',
        'utm',
        'scheduled_at',
        'published_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'utm' => 'array',
            'scheduled_at' => 'datetime',
            'published_at' => 'datetime',
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

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approver_id');
    }

    public function contentDestinations(): HasMany
    {
        return $this->hasMany(ContentDestination::class);
    }

    public function mediaAssets(): BelongsToMany
    {
        return $this->belongsToMany(MediaAsset::class, 'content_item_media')
            ->withPivot('sort_order')
            ->withTimestamps()
            ->orderByPivot('sort_order');
    }

    public function trackedLinks(): HasMany
    {
        return $this->hasMany(TrackedLink::class);
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
}
