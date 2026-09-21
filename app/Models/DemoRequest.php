<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DemoRequest extends Model
{
    use HasFactory;

    public const STATUSES = [
        'requested',
        'contacted',
        'scheduled',
        'confirmed',
        'completed',
        'no_show',
        'reschedule',
        'converted',
        'lost',
    ];

    /**
     * @var list<string>
     */
    protected $fillable = [
        'contact_message_id',
        'product_id',
        'campaign_id',
        'request_type',
        'preferred_at',
        'scheduled_at',
        'assigned_to',
        'status',
        'notes',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'preferred_at' => 'datetime',
            'scheduled_at' => 'datetime',
        ];
    }

    public function contactMessage(): BelongsTo
    {
        return $this->belongsTo(ContactMessage::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
