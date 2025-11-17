<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaterialAccess extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'learning_material_id',
        'student_id',
        'action',
        'accessed_at',
    ];

    protected $casts = [
        'accessed_at' => 'datetime',
    ];

    public $timestamps = false;

    /**
     * Relationships
     */
    public function material()
    {
        return $this->belongsTo(LearningMaterial::class, 'learning_material_id');
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}
