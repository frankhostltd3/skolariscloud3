<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayrollSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'category',
        'key',
        'value',
        'type',
        'label',
        'description',
        'is_active',
        'sort_order',
        'validation_rules',
        'options',
    ];

    protected $casts = [
        'value' => 'json',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'validation_rules' => 'json',
        'options' => 'json',
    ];

    /**
     * Get settings by category
     */
    public static function getByCategory(string $category): \Illuminate\Database\Eloquent\Collection
    {
        return static::where('category', $category)
                    ->where('is_active', true)
                    ->orderBy('sort_order')
                    ->get();
    }

    /**
     * Get setting value by key
     */
    public static function getValue(string $key, $default = null)
    {
        $setting = static::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    /**
     * Set setting value by key
     */
    public static function setValue(string $key, $value): bool
    {
        $setting = static::where('key', $key)->first();

        if ($setting) {
            $setting->update(['value' => $value]);
            return true;
        }

        return false;
    }

    /**
     * Get all active settings grouped by category
     */
    public static function getGroupedSettings(): array
    {
        $settings = static::where('is_active', true)
                         ->orderBy('category')
                         ->orderBy('sort_order')
                         ->get();

        return $settings->groupBy('category')->toArray();
    }
}
