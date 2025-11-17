<?php

namespace App\Models\Academic;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExaminationBody extends Model
{
    use HasFactory;

    protected $connection = 'tenant';

    protected $fillable = [
        'school_id',
        'name',
        'code',
        'country_id',
        'website',
        'description',
        'is_international',
        'is_active',
    ];

    protected $casts = [
        'is_international' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function school()
    {
        return $this->belongsTo(\App\Models\School::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * Scope a query to only include bodies for a specific school.
     */
    public function scopeForSchool($query, $schoolId)
    {
        return $query->where('school_id', $schoolId);
    }

    /**
     * Scope a query to only include active examination bodies.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include international examination bodies.
     */
    public function scopeInternational($query)
    {
        return $query->where('is_international', true);
    }

    /**
     * Scope a query to filter by country.
     */
    public function scopeByCountry($query, $countryId)
    {
        return $query->where('country_id', $countryId);
    }
}
