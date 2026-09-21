<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class MetricSnapshot extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'measurable_type',
        'measurable_id',
        'source',
        'metric_key',
        'raw_key',
        'value',
        'captured_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'value' => 'decimal:4',
            'captured_at' => 'datetime',
        ];
    }

    public function measurable(): MorphTo
    {
        return $this->morphTo();
    }
}
