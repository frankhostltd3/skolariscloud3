<?php

namespace App\Models\Academic;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcademicYear extends Model
{
    use HasFactory;

    protected $connection = 'tenant';

    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'is_current',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_current' => 'boolean',
    ];

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class, 'academic_year_id');
    }

    public function terms()
    {
        return $this->hasMany(\App\Models\Term::class, 'academic_year_id');
    }

    public function scopeCurrent($query)
    {
        return $query->where('is_current', true);
    }
}
