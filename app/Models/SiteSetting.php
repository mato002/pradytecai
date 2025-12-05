<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    protected $fillable = [
        'key',
        'value',
    ];

    public $timestamps = false;

    /**
     * Get a setting value by key.
     */
    public static function get(string $key, $default = null)
    {
        $record = static::query()->where('key', $key)->first();

        return $record?->value ?? $default;
    }

    /**
     * Set/update a setting value.
     */
    public static function set(string $key, $value): void
    {
        static::query()->updateOrCreate(
            ['key' => $key],
            ['value' => $value],
        );
    }
}



