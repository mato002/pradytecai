<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeadCommunication extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'contact_message_id',
        'user_id',
        'channel',
        'subject',
        'body',
        'status',
    ];

    public function contactMessage(): BelongsTo
    {
        return $this->belongsTo(ContactMessage::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
