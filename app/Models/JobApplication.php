<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'position_id',
        'name',
        'email',
        'phone',
        'cover_letter',
        'resume_path',
        'status',
        'interview_scheduled_at',
        'interview_notes',
        'admin_notes',
        'rating',
    ];

    protected $casts = [
        'interview_scheduled_at' => 'datetime',
        'rating' => 'integer',
    ];

    /**
     * Get the position this application is for
     */
    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    /**
     * Scope to filter by status
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to get recent applications
     */
    public function scopeRecent($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Get comments for this application
     */
    public function comments()
    {
        return $this->hasMany(\App\Models\ApplicationComment::class)->whereNull('parent_id')->orderByDesc('created_at');
    }

    /**
     * Get all comments including replies
     */
    public function allComments()
    {
        return $this->hasMany(\App\Models\ApplicationComment::class)->orderByDesc('created_at');
    }
}


