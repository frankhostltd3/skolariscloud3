<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassStream extends Model
{
    use HasFactory;

    protected $fillable = ['class_id','name','max_capacity'];

    public function class()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    /**
     * Get all students in this stream
     */
    public function students()
    {
        return $this->hasMany(Student::class, 'class_stream_id');
    }
}
