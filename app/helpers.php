<?php

use App\Helpers\ImageHelper;

if (! function_exists('image_url')) {
    function image_url(
        ?string $path,
        string $default = 'images/default-image.png'
    ): string {
        return ImageHelper::url($path, $default);
    }
}