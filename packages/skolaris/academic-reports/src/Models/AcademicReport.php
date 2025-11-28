<?php

namespace Skolaris\AcademicReports\Models;

use Illuminate\Database\Eloquent\Model;

class AcademicReport extends Model
{
    protected $guarded = [];

    public function term()
    {
        return $this->belongsTo(config('skolaris_reports.term_model', AcademicTerm::class), 'term_id');
    }

    public function marks()
    {
        return $this->hasMany(ReportMark::class, 'report_id');
    }

    public function student()
    {
        // Assumes the host app has a User model or configured model
        return $this->belongsTo(config('skolaris_reports.user_model'), 'student_id');
    }
}
