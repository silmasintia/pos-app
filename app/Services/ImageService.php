<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Http\UploadedFile;

class ImageService
{
    public function saveWebp(UploadedFile $file, string $path, string $suffix = '', int $quality = 75): string
    {
        $fileName = uniqid() . time() . $suffix . '.webp';
        $destinationPath = rtrim($path, '/') . '/' . $fileName;

        $image = Image::read($file)->toWebp($quality);

        Storage::disk('public')->put($destinationPath, (string) $image);

        return $destinationPath;
    }

    public function deleteFile(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}