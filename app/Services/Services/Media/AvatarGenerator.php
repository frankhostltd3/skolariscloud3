<?php

namespace App\Services\Media;

use Illuminate\Support\Facades\Storage;

class AvatarGenerator
{
    /**
     * Generate a circular avatar PNG (256x256) with initials.
     * Returns relative path under public disk.
     */
    public function generate(string $firstName, string $lastName, string $directory = 'employee-avatars'): string
    {
        $size = 256;
        $canvas = imagecreatetruecolor($size, $size);
        imagealphablending($canvas, true);
        imagesavealpha($canvas, true);

        // Background color (deterministic from name)
        $hash = crc32(strtolower($firstName.$lastName));
        $r = ($hash & 0xFF0000) >> 16;
        $g = ($hash & 0x00FF00) >> 8;
        $b = ($hash & 0x0000FF);
        $bg = imagecolorallocate($canvas, $r, $g, $b);
        imagefilledrectangle($canvas, 0, 0, $size, $size, $bg);

        // Circle mask
        $mask = imagecreatetruecolor($size, $size);
        imagesavealpha($mask, true);
        $transparent = imagecolorallocatealpha($mask, 0,0,0,127);
        imagefill($mask,0,0,$transparent);
        $circleColor = imagecolorallocatealpha($mask, 0,0,0,0);
        imagefilledellipse($mask, $size/2, $size/2, $size, $size, $circleColor);

        // Apply mask via alpha channel
        imagecolortransparent($mask, $transparent);

        // Merge mask (simple approach: copy only circle area)
        for ($x=0; $x<$size; $x++) {
            for ($y=0; $y<$size; $y++) {
                $alpha = imagecolorat($mask, $x, $y) & 0x7F000000;
                // no-op; circle fully opaque
            }
        }

        // Text
        $initials = strtoupper(mb_substr($firstName,0,1).mb_substr($lastName,0,1));
        $textColor = imagecolorallocate($canvas, 255,255,255);
        $fontSize = 90; // GD imagettftext size
        $font = __DIR__.'/../../Resources/Fonts/OpenSans-Bold.ttf';
        if (!file_exists($font)) {
            // fallback built-in font positioning
            imagestring($canvas, 5, 100, 110, $initials, $textColor);
        } else {
            $bbox = imagettfbbox($fontSize, 0, $font, $initials);
            $textWidth = $bbox[2] - $bbox[0];
            $textHeight = $bbox[1] - $bbox[7];
            $x = ($size - $textWidth) / 2;
            $y = ($size + $textHeight) / 2 - 10;
            imagettftext($canvas, $fontSize, 0, (int)$x, (int)$y, $textColor, $font, $initials);
        }

        ob_start();
        imagepng($canvas, null, 9);
        $png = ob_get_clean();
        imagedestroy($canvas);
        imagedestroy($mask);

        $filename = uniqid('av_', true) . '.png';
        $path = $directory . '/' . $filename;
        Storage::disk('public')->put($path, $png, 'public');
        return $path;
    }
}
