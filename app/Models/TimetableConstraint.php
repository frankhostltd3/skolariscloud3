<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimetableConstraint extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'name',
        'description',
        'constraints',
        'is_active',
    ];

    protected $casts = [
        'constraints' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Scope active constraints
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope by type
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }
}
