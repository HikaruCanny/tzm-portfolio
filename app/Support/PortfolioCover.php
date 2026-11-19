<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;

final class PortfolioCover
{
    public const BLOG = 'images/defaults/blog-cover.svg';

    public const PROJECT = 'images/defaults/project-cover.svg';

    public static function assetUrl(?string $storagePath, string $defaultRelativePath): string
    {
        if ($storagePath !== null && $storagePath !== '') {
            return Storage::disk('public')->url($storagePath);
        }

        return asset($defaultRelativePath);
    }

    public static function absoluteUrl(?string $storagePath, string $defaultRelativePath): string
    {
        return url(self::assetUrl($storagePath, $defaultRelativePath));
    }
}
