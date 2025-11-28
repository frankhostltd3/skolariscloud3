<?php

namespace App\Services\Media;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageService
{
    /**
     * Process and store a passport photo.
     *
     * @param UploadedFile $file
     * @return string
     */
    public function processPassportPhoto(UploadedFile $file): string
    {
        // Generate a unique filename
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();

        // Store the file in the 'public/passport_photos' directory
        // This will be accessible via storage/passport_photos if the link is created
        $path = $file->storeAs('passport_photos', $filename, 'public');

        // Return the storage path (relative to the disk root)
        return $path;
    }
}
