<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Campaign extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'objective',
        'owner_id',
        'starts_at',
        'ends_at',
        'status',
        'audience_notes',
        'budget_amount',
        'utm_campaign',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'budget_amount' => 'decimal:2',
        ];
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'campaign_product')
            ->withTimestamps();
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

    public function metricSnapshots(): MorphMany
    {
        return $this->morphMany(MetricSnapshot::class, 'measurable');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        $productIds = $user->scopedProductIds();

        if ($productIds === null) {
            return $query;
        }

        return $query->whereHas('products', function (Builder $products) use ($productIds) {
            $products->whereIn('products.id', $productIds);
        });
    }
}
