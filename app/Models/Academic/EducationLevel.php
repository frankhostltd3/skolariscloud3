<?php

namespace App\Models\Academic;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EducationLevel extends Model
{
    use HasFactory;

    protected $connection = 'tenant';

    protected $fillable = [
        'school_id',
        'name',
        'code',
        'description',
        'min_grade',
        'max_grade',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'min_grade' => 'integer',
        'max_grade' => 'integer',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function school()
    {
        return $this->belongsTo(\App\Models\School::class);
    }

    public function classes()
    {
        return $this->hasMany(\App\Models\Academic\ClassRoom::class);
    }

    public function subjects()
    {
        return $this->hasMany(\App\Models\Academic\Subject::class);
    }
}
