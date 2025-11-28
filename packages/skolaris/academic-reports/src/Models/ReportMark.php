<?php

namespace Skolaris\AcademicReports\Models;

use Illuminate\Database\Eloquent\Model;

class ReportMark extends Model
{
    protected $guarded = [];

    public function subject()
    {
        return $this->belongsTo(config('skolaris_reports.subject_model', Subject::class), 'subject_id');
    }
}
