<?php

namespace App\Services;

use GdImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class StoresPortfolioImage
{
    public function store(UploadedFile $file, string $directory): string
    {
        if (! function_exists('imagecreatefromstring') || ! function_exists('imagewebp')) {
            throw ValidationException::withMessages([
                'cover_image' => 'This server cannot convert images to WebP.',
            ]);
        }

        $contents = file_get_contents($file->getRealPath());

        if ($contents === false) {
            throw ValidationException::withMessages([
                'cover_image' => 'The image could not be read.',
            ]);
        }

        $source = @imagecreatefromstring($contents);

        if ($source === false) {
            throw ValidationException::withMessages([
                'cover_image' => 'The image could not be processed.',
            ]);
        }

        $resized = $this->resize($source, 1600);
        imagepalettetotruecolor($resized);
        imagealphablending($resized, true);
        imagesavealpha($resized, true);

        ob_start();
        $written = imagewebp($resized, null, 82);
        $binary = ob_get_clean();
        imagedestroy($resized);

        if ($written !== true || ! is_string($binary) || $binary === '') {
            throw ValidationException::withMessages([
                'cover_image' => 'The image could not be converted to WebP.',
            ]);
        }

        $path = trim($directory, '/').'/'.Str::uuid()->toString().'.webp';
        Storage::disk('public')->put($path, $binary);

        return $path;
    }

    public function delete(?string $path): void
    {
        if (is_string($path) && $path !== '') {
            Storage::disk('public')->delete($path);
        }
    }

    private function resize(GdImage $source, int $maxWidth): GdImage
    {
        $width = imagesx($source);
        $height = imagesy($source);

        if ($maxWidth < 1 || $width <= $maxWidth) {
            return $source;
        }

        $newHeight = (int) round($height * ($maxWidth / $width));

        if ($newHeight < 1) {
            return $source;
        }

        $canvas = imagecreatetruecolor($maxWidth, $newHeight);

        imagealphablending($canvas, false);
        imagesavealpha($canvas, true);
        imagecopyresampled($canvas, $source, 0, 0, 0, 0, $maxWidth, $newHeight, $width, $height);
        imagedestroy($source);

        return $canvas;
    }
}
