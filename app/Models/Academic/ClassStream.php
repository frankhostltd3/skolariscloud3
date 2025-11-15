<?php

namespace App\Models\Academic;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassStream extends Model
{
    use HasFactory;

    protected $connection = 'tenant';

    protected $fillable = [
        'class_id',
        'name',
        'code',
        'description',
        'capacity',
        'active_students_count',
        'is_active',
    ];

    protected $casts = [
        'capacity' => 'integer',
        'active_students_count' => 'integer',
        'is_active' => 'boolean',
    ];

    public function class()
    {
        return $this->belongsTo(\App\Models\Academic\ClassRoom::class, 'class_id');
    }

    public function students()
    {
        return $this->hasMany(\App\Models\Student::class, 'stream_id');
    }

    public function getFullNameAttribute()
    {
        return $this->class->name . ' ' . $this->name;
    }
}
