<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class LearningMaterial extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'teacher_id',
        'class_id',
        'subject_id',
        'title',
        'description',
        'type',
        'file_path',
        'file_size',
        'file_mime',
        'external_url',
        'youtube_id',
        'is_downloadable',
        'views_count',
        'downloads_count',
    ];

    protected $casts = [
        'is_downloadable' => 'boolean',
        'views_count' => 'integer',
        'downloads_count' => 'integer',
    ];

    protected $appends = ['type_label', 'file_size_formatted', 'file_url'];

    /**
     * Relationships
     */
    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function class()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function accesses()
    {
        return $this->hasMany(MaterialAccess::class);
    }

    /**
     * Scopes
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByTeacher($query, $teacherId)
    {
        return $query->where('teacher_id', $teacherId);
    }

    public function scopeByClass($query, $classId)
    {
        return $query->where('class_id', $classId);
    }

    public function scopeDownloadable($query)
    {
        return $query->where('is_downloadable', true);
    }

    /**
     * Accessors
     */
    public function getTypeLabelAttribute()
    {
        return match($this->type) {
            'document' => 'Document',
            'video' => 'Video',
            'audio' => 'Audio',
            'image' => 'Image',
            'link' => 'External Link',
            'youtube' => 'YouTube Video',
            default => 'Unknown'
        };
    }

    public function getFileSizeFormattedAttribute()
    {
        if (!$this->file_size) {
            return 'N/A';
        }

        $size = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;

        while ($size >= 1024 && $i < count($units) - 1) {
            $size /= 1024;
            $i++;
        }

        return round($size, 2) . ' ' . $units[$i];
    }

    public function getFileUrlAttribute()
    {
        if ($this->file_path) {
            return Storage::url($this->file_path);
        }

        if ($this->youtube_id) {
            return 'https://www.youtube.com/watch?v=' . $this->youtube_id;
        }

        return $this->external_url;
    }

    public function getYoutubeEmbedUrlAttribute()
    {
        if ($this->youtube_id) {
            return 'https://www.youtube.com/embed/' . $this->youtube_id;
        }
        return null;
    }

    /**
     * Methods
     */
    public function incrementViews()
    {
        $this->increment('views_count');
    }

    public function incrementDownloads()
    {
        $this->increment('downloads_count');
    }

    public function recordAccess($studentId, $action = 'view')
    {
        return $this->accesses()->create([
            'student_id' => $studentId,
            'action' => $action,
            'accessed_at' => now(),
        ]);
    }

    public function getAccessesByStudent($studentId)
    {
        return $this->accesses()->where('student_id', $studentId)->get();
    }

    public function deleteFile()
    {
        if ($this->file_path && Storage::exists($this->file_path)) {
            Storage::delete($this->file_path);
        }
    }
}
