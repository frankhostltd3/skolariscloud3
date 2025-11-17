<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use App\Models\Academic\AcademicYear;
use App\Models\Academic\Semester;
use App\Models\Academic\Term;

class ReportCard extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'academic_year_id',
        'semester_id',
        'term_id',
        'title',
        'file_path',
        'file_name',
        'file_size',
        'mime_type',
        'report_type',
        'generated_by',
        'generated_at',
        'metadata',
    ];

    protected $casts = [
        'generated_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    public function term()
    {
        return $this->belongsTo(Term::class);
    }

    public function generatedBy()
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

    public function getDownloadUrlAttribute()
    {
        return route('parent.documents.download', $this->id);
    }

    public function getFileSizeHumanAttribute()
    {
        $bytes = $this->file_size;
        if ($bytes >= 1024 * 1024) {
            return round($bytes / (1024 * 1024), 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return round($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }

    public function exists(): bool
    {
        return Storage::disk('local')->exists($this->file_path);
    }

    public function delete(): bool
    {
        // Delete the physical file when deleting the record
        if ($this->exists()) {
            Storage::disk('local')->delete($this->file_path);
        }
        return parent::delete();
    }
}