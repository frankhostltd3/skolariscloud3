<?php

namespace App\Models\Academic;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;

    protected $connection = 'tenant';

    protected $fillable = [
        'name',
        'iso_code_2',
        'iso_code_3',
        'phone_code',
        'currency_code',
        'currency_symbol',
        'timezone',
        'flag_emoji',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function examinationBodies()
    {
        return $this->hasMany(ExaminationBody::class);
    }

    /**
     * Scope a query to only include active countries.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get the full country name with flag emoji.
     */
    public function getFullNameAttribute(): string
    {
        return $this->flag_emoji ? "{$this->flag_emoji} {$this->name}" : $this->name;
    }
}
