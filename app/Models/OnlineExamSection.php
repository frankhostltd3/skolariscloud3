<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OnlineExamSection extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'online_exam_id',
        'title',
        'description',
        'order',
    ];

    /**
     * Relationships
     */
    public function exam()
    {
        return $this->belongsTo(OnlineExam::class, 'online_exam_id');
    }

    public function questions()
    {
        return $this->hasMany(OnlineExamQuestion::class)->orderBy('order');
    }

    /**
     * Methods
     */
    public function getTotalMarks()
    {
        return $this->questions()->sum('marks');
    }

    public function getQuestionsCount()
    {
        return $this->questions()->count();
    }
}
