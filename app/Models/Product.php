<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'short',
        'market',
        'code',
        'type',
        'url',
        'is_active',
        'order',
        'last_marketed_at',
        'features',
        'benefits',
        'statistics',
        'button_text',
        'icon',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'order' => 'integer',
            'last_marketed_at' => 'datetime',
            'features' => 'array',
            'benefits' => 'array',
            'statistics' => 'array',
        ];
    }

    public function campaigns(): BelongsToMany
    {
        return $this->belongsToMany(Campaign::class, 'campaign_product')
            ->withTimestamps();
    }

    public function socialAccounts(): HasMany
    {
        return $this->hasMany(SocialAccount::class);
    }

    public function contentItems(): HasMany
    {
        return $this->hasMany(ContentItem::class);
    }

    public function contactMessages(): HasMany
    {
        return $this->hasMany(ContactMessage::class);
    }

    public function demoRequests(): HasMany
    {
        return $this->hasMany(DemoRequest::class);
    }

    public function marketingAlerts(): HasMany
    {
        return $this->hasMany(MarketingAlert::class);
    }

    public function marketingTasks(): HasMany
    {
        return $this->hasMany(MarketingTask::class);
    }

    public function trackedLinks(): HasMany
    {
        return $this->hasMany(TrackedLink::class);
    }

    public function websiteEvents(): HasMany
    {
        return $this->hasMany(WebsiteEvent::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('order')->orderBy('name');
    }

    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        $productIds = $user->scopedProductIds();

        if ($productIds === null) {
            return $query;
        }

        return $query->whereIn('id', $productIds);
    }
}
