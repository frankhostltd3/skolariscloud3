<?php

namespace App\Services\Media;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ImageService
{
    /**
     * Resize and store an uploaded image to max 256x256 while preserving aspect ratio.
     * Returns relative path under public disk.
     */
    public function processPassportPhoto(UploadedFile $file, string $directory = 'employee-photos'): string
    {
        $imageData = file_get_contents($file->getRealPath());
        $source = @imagecreatefromstring($imageData);
        if (!$source) {
            throw new \RuntimeException('Invalid image data.');
        }
        $srcW = imagesx($source);
        $srcH = imagesy($source);
        $max = 256;
        // Determine scale
        $scale = min($max / $srcW, $max / $srcH, 1); // never upscale
        $targetW = (int) floor($srcW * $scale);
        $targetH = (int) floor($srcH * $scale);
        $canvas = imagecreatetruecolor($targetW, $targetH);
        imagealphablending($canvas, false);
        imagesavealpha($canvas, true);
        $transparent = imagecolorallocatealpha($canvas, 0,0,0,127);
        imagefilledrectangle($canvas, 0,0,$targetW,$targetH,$transparent);
        imagecopyresampled($canvas, $source, 0, 0, 0, 0, $targetW, $targetH, $srcW, $srcH);
        ob_start();
        imagepng($canvas, null, 9);
        $pngData = ob_get_clean();
        imagedestroy($canvas);
        imagedestroy($source);

        $filename = uniqid('pp_', true) . '.png';
        $path = $directory . '/' . $filename;
        Storage::disk('public')->put($path, $pngData, 'public');
        return $path;
    }
}
