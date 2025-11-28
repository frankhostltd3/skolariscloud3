<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileUploadService
{
    /**
     * Upload a file to specified disk and path.
     */
    public function upload(UploadedFile $file, string $path = 'classroom', string $disk = 'public'): array
    {
        $filename = $this->generateUniqueFilename($file);
        $filePath = $file->storeAs($path, $filename, $disk);

        return [
            'path' => $filePath,
            'filename' => $filename,
            'original_name' => $file->getClientOriginalName(),
            'size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'extension' => $file->getClientOriginalExtension(),
        ];
    }

    /**
     * Upload multiple files.
     */
    public function uploadMultiple(array $files, string $path = 'classroom', string $disk = 'public'): array
    {
        $uploadedFiles = [];

        foreach ($files as $file) {
            if ($file instanceof UploadedFile) {
                $uploadedFiles[] = $this->upload($file, $path, $disk);
            }
        }

        return $uploadedFiles;
    }

    /**
     * Delete a file.
     */
    public function delete(string $filePath, string $disk = 'public'): bool
    {
        if (Storage::disk($disk)->exists($filePath)) {
            return Storage::disk($disk)->delete($filePath);
        }

        return false;
    }

    /**
     * Delete multiple files.
     */
    public function deleteMultiple(array $filePaths, string $disk = 'public'): void
    {
        foreach ($filePaths as $filePath) {
            $this->delete($filePath, $disk);
        }
    }

    /**
     * Get file URL.
     */
    public function getUrl(string $filePath, string $disk = 'public'): ?string
    {
        if (Storage::disk($disk)->exists($filePath)) {
            return Storage::disk($disk)->url($filePath);
        }

        return null;
    }

    /**
     * Check if file exists.
     */
    public function exists(string $filePath, string $disk = 'public'): bool
    {
        return Storage::disk($disk)->exists($filePath);
    }

    /**
     * Get file size.
     */
    public function getSize(string $filePath, string $disk = 'public'): ?int
    {
        return $this->exists($filePath, $disk)
            ? Storage::disk($disk)->size($filePath)
            : null;
    }

    /**
     * Generate a unique filename for upload.
     */
    protected function generateUniqueFilename(UploadedFile $file): string
    {
        $extension = $file->getClientOriginalExtension();
        $basename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $basename = Str::slug($basename);

        return $basename . '_' . time() . '_' . Str::random(8) . '.' . $extension;
    }

    /**
     * Validate file type.
     */
    public function validateFileType(UploadedFile $file, array $allowedTypes): bool
    {
        $mimeType = $file->getMimeType();
        $extension = strtolower($file->getClientOriginalExtension());

        foreach ($allowedTypes as $type) {
            if (Str::contains($mimeType, $type) || $extension === $type) {
                return true;
            }
        }

        return false;
    }

    /**
     * Validate file size.
     */
    public function validateFileSize(UploadedFile $file, int $maxSizeInMB): bool
    {
        $maxSizeInBytes = $maxSizeInMB * 1024 * 1024;
        return $file->getSize() <= $maxSizeInBytes;
    }

    /**
     * Allowed document mime types.
     */
    public function getDocumentMimeTypes(): array
    {
        return [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.ms-powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'text/plain',
        ];
    }

    /**
     * Allowed image mime types.
     */
    public function getImageMimeTypes(): array
    {
        return [
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/svg+xml',
            'image/webp',
        ];
    }

    /**
     * Allowed video mime types.
     */
    public function getVideoMimeTypes(): array
    {
        return [
            'video/mp4',
            'video/mpeg',
            'video/quicktime',
            'video/x-msvideo',
            'video/webm',
        ];
    }

    /**
     * Allowed audio mime types.
     */
    public function getAudioMimeTypes(): array
    {
        return [
            'audio/mpeg',
            'audio/wav',
            'audio/mp4',
            'audio/webm',
        ];
    }

    /**
     * Format file size for display.
     */
    public function formatFileSize(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i = 0;

        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Extract a YouTube video ID from a URL.
     */
    public function extractYoutubeId(string $url): ?string
    {
        $patterns = [
            '/youtube\.com\/watch\?v=([a-zA-Z0-9_-]+)/',
            '/youtube\.com\/embed\/([a-zA-Z0-9_-]+)/',
            '/youtu\.be\/([a-zA-Z0-9_-]+)/',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $url, $matches)) {
                return $matches[1];
            }
        }

        return null;
    }
}
