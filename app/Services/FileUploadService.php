<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class FileUploadService
{
    public function upload(
        UploadedFile $file,
        string $directory,
        string $disk = 'public'
    ): string {
        return $file->store($directory, $disk);
    }

    public function delete(
        ?string $path,
        string $disk = 'public'
    ): bool {
        if (!$path) {
            return false;
        }

        return Storage::disk($disk)->delete($path);
    }

    public function replace(
        UploadedFile $file,
        ?string $oldPath,
        string $directory,
        string $disk = 'public'
    ): string {
        if ($oldPath) {
            $this->delete($oldPath, $disk);
        }

        return $this->upload($file, $directory, $disk);
    }

    public function url(
        ?string $path,
        string $disk = 'public'
    ): ?string {
        if (!$path) {
            return null;
        }

        return Storage::disk($disk)->url($path);
    }
}