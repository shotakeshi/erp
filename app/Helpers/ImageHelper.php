<?php

namespace App\Helpers;

class ImageHelper
{
    /**
     * Generate image URL.
     *
     * Supported:
     * - employees/avatars/avatar.jpg
     * - storage/employees/avatars/avatar.jpg
     * - /storage/employees/avatars/avatar.jpg
     * - https://example.com/avatar.jpg
     * - null / empty
     */
    public static function url(
        ?string $path,
        string $default = 'images/default-image.png'
    ): string {
        // No image
        if (blank($path)) {
            return asset($default);
        }

        // External URL
        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }

        $path = trim($path);

        // Already contains /storage/
        if (str_starts_with($path, '/storage/')) {
            return asset(ltrim($path, '/'));
        }

        // Already contains storage/
        if (str_starts_with($path, 'storage/')) {
            return asset($path);
        }

        // Laravel public storage
        return asset('storage/' . ltrim($path, '/'));
    }
}