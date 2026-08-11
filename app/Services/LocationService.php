<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;

class LocationService
{
    protected array $provinces;
    protected array $wards;

    public function __construct()
    {
        $this->provinces = Cache::rememberForever('locations.provinces', function () {
            return json_decode(
                File::get(public_path('provinces/province.json')),
                true
            );
        });

        $this->wards = Cache::rememberForever('locations.wards', function () {
            return json_decode(
                File::get(public_path('provinces/ward.json')),
                true
            );
        });
    }

    /**
     * Danh sách tỉnh/thành
     */
    public function provinces(): array
    {
        return $this->provinces;
    }

    /**
     * Danh sách phường/xã
     */
    public function wards(): array
    {
        return $this->wards;
    }

    /**
     * Lấy phường/xã theo tỉnh
     */
    public function wardsByProvince(string $provinceCode): array
    {
        return array_values(array_filter(
            $this->wards,
            fn ($ward) => $ward['parent_code'] === $provinceCode
        ));
    }

    /**
     * Lấy thông tin tỉnh
     */
    public function province(string $code): ?array
    {
        return $this->provinces[$code] ?? null;
    }

    /**
     * Lấy thông tin phường/xã
     */
    public function ward(string $code): ?array
    {
        return $this->wards[$code] ?? null;
    }
}