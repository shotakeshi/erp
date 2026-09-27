<?php
if (!function_exists('format_phone')) {
    function format_phone(?string $phone): ?string
    {
        if (blank($phone)) {
            return null;
        }

        $phone = preg_replace('/\D+/', '', $phone);

        if (str_starts_with($phone, '84')) {
            $phone = '0' . substr($phone, 2);
        }

        if (!preg_match('/^0[35789]\d{8}$/', $phone)) {
            return $phone;
        }

        return preg_replace(
            '/^(\d{3})(\d{3})(\d{4})$/',
            '$1 $2 $3',
            $phone
        );
    }
}