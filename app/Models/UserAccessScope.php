<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class UserAccessScope extends Model
{
    protected $fillable = [
        'user_id',
        'scope_type',
        'scope_id',
    ];

    public const TYPE_PRODUCT = 'product';

    public const TYPE_SOCIAL_ACCOUNT = 'social_account';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeable(): MorphTo
    {
        return $this->morphTo(__FUNCTION__, 'scope_type', 'scope_id');
    }
}
