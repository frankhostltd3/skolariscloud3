<?php

namespace Skolaris\AcademicReports\Models;

use Illuminate\Database\Eloquent\Model;

class StudentFee extends Model
{
    protected $guarded = [];

    public function term()
    {
        return $this->belongsTo(AcademicTerm::class);
    }
}
