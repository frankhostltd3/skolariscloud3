<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExerciseAttachment extends Model
{
    use HasFactory;

    protected $connection = 'tenant';

    protected $fillable = [
        'exercise_id',
        'filename',
        'original_name',
        'mime_type',
        'file_size',
        'path',
    ];

    /**
     * Relationships
     */
    public function exercise()
    {
        return $this->belongsTo(Exercise::class);
    }

    /**
     * Get human-readable file size
     */
    public function getHumanFileSizeAttribute(): string
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Get file URL
     */
    public function getUrlAttribute(): string
    {
        return asset('storage/' . $this->path);
    }

    /**
     * Check if file is an image
     */
    public function getIsImageAttribute(): bool
    {
        return str_starts_with($this->mime_type, 'image/');
    }

    /**
     * Check if file is a PDF
     */
    public function getIsPdfAttribute(): bool
    {
        return $this->mime_type === 'application/pdf';
    }

    /**
     * Get file icon class based on type
     */
    public function getIconClassAttribute(): string
    {
        return match(true) {
            $this->is_image => 'bi-file-earmark-image text-success',
            $this->is_pdf => 'bi-file-earmark-pdf text-danger',
            str_contains($this->mime_type, 'word') => 'bi-file-earmark-word text-primary',
            str_contains($this->mime_type, 'excel') || str_contains($this->mime_type, 'spreadsheet') => 'bi-file-earmark-excel text-success',
            str_contains($this->mime_type, 'powerpoint') || str_contains($this->mime_type, 'presentation') => 'bi-file-earmark-ppt text-warning',
            default => 'bi-file-earmark text-secondary',
        };
    }
}
