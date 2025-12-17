<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ApplicationComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_application_id',
        'user_id',
        'parent_id',
        'comment',
        'is_internal',
    ];

    /**
     * Get the job application this comment belongs to
     */
    public function jobApplication(): BelongsTo
    {
        return $this->belongsTo(JobApplication::class);
    }

    /**
     * Get the user who made the comment
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the parent comment (for threaded replies)
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(ApplicationComment::class, 'parent_id');
    }

    /**
     * Get child comments (replies)
     */
    public function replies(): HasMany
    {
        return $this->hasMany(ApplicationComment::class, 'parent_id')->orderBy('created_at');
    }

    /**
     * Scope to get only top-level comments (no parent)
     */
    public function scopeTopLevel($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Scope to filter by internal/external
     */
    public function scopeInternal($query, bool $internal = true)
    {
        return $query->where('is_internal', $internal);
    }
}

