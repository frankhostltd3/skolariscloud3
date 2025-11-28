<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeIdSetting extends Model
{
    protected $connection = 'tenant';

    protected $fillable = [
        'template_name',
        'card_width',
        'card_height',
        'background_color',
        'text_color',
        'header_text',
        'header_color',
        'logo_path',
        'fields_to_display',
        'include_qr_code',
        'qr_code_position',
        'qr_code_size',
        'include_photo',
        'photo_position',
        'photo_size',
        'font_family',
        'font_size',
        'layout_settings',
        'is_default',
        'is_active',
    ];

    protected $casts = [
        'fields_to_display' => 'array',
        'include_qr_code' => 'boolean',
        'include_photo' => 'boolean',
        'is_default' => 'boolean',
        'is_active' => 'boolean',
        'layout_settings' => 'array',
        'card_width' => 'decimal:2',
        'card_height' => 'decimal:2',
        'qr_code_size' => 'integer',
        'photo_size' => 'integer',
        'font_size' => 'integer',
    ];

    /**
     * Get the default template
     */
    public static function getDefault()
    {
        return static::where('is_default', true)->where('is_active', true)->first()
            ?? static::where('is_active', true)->first();
    }

    /**
     * Get active templates
     */
    public static function getActive()
    {
        return static::where('is_active', true)
            ->orderByDesc('is_default')
            ->orderBy('template_name')
            ->get();
    }
}
