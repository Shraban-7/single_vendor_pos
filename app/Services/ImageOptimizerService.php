<?php

namespace App\Services;

use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ImageOptimizerService
{
    const IMAGE_QUALITY = 85;
    /**
     * Optimize and upload an image.
     *
     * @param UploadedFile $file The uploaded file.
     * @param string $path The storage path (e.g., 'products').
     * @param int|null $width Max width (optional).
     * @param int|null $height Max height (optional).
     * @return string The stored file path.
     */
    public function uploadAndOptimize(UploadedFile $file, string $path, ?int $width = 1200, ?int $height = null): string
    {
        $filename = Str::uuid() . '.webp';
        $fullPath = $path . '/' . $filename;

        $image = Image::read($file);

        // Resize the image strictly if width is provided, maintaining aspect ratio
        // This prevents vendors from uploading 6000px wide raw photos
        if ($width) {
            $image->scaleDown(width: $width, height: $height);
        }

        // Encode to WebP with 80-90% quality
        // WebP offers superior compression (30% smaller than JPEG) with comparable quality
        $encoded = $image->toWebp(quality: $this::IMAGE_QUALITY);

        Storage::disk('public')->put($fullPath, (string) $encoded);

        return $fullPath;
    }

    public function optimizeExistingImage(string $existingPath, bool $webp = false): null|string
    {
        $disk = Storage::disk('public');

        if (! $disk->exists($existingPath)) {
            return null;
        }

        $image = Image::read($disk->get($existingPath));
        $extension = strtolower(pathinfo($existingPath, PATHINFO_EXTENSION));

        if ($webp === true) {
            $newPath = preg_replace('/\.[^.]+$/', '.webp', $existingPath);

            $encoded = $image->toWebp($this::IMAGE_QUALITY);

            $disk->put($newPath, (string) $encoded);
            $disk->delete($existingPath);

            return $newPath;
        }

        // Optimize in-place (same extension)
        $encoded = match ($extension) {
            'jpg', 'jpeg' => $image->toJpeg($this::IMAGE_QUALITY),
            'png' => $image->toPng(),
            'gif' => $image->toGif(),
            default => throw new \Exception("Unsupported image type: {$extension}")
        };

        $disk->put($existingPath, (string) $encoded);

        return $existingPath;
    }

    /**
     * Optimize an existing image in storage.
     *
     * @param string $existingPath Path relative to disk (e.g. 'products/image.jpg')
     * @param string|null $newPath Optional new directory (defaults to same folder)
     * @param int|null $width Max width
     * @param int|null $height Max height
     * @param bool $overwrite Whether to overwrite the original file
     * @return string Path to optimized image
     */
    public function optimizeExisting(string $existingPath, ?string $newPath = null, ?int $width = 1200, ?int $height = null, bool $overwrite = true): string
    {
        $disk = Storage::disk('public');

        if (! $disk->exists($existingPath)) {
            return $existingPath;
            //throw new \InvalidArgumentException("Image does not exist: {$existingPath}");
        }

        $image = Image::read($disk->get($existingPath));

        // Resize if needed
        if ($width) {
            $image->scaleDown(width: $width, height: $height);
        }

        // Encode to WebP
        $encoded = $image->toWebp(quality: 85);

        if ($overwrite) {
            // Replace original file
            $optimizedPath = preg_replace('/\.\w+$/', '.webp', $existingPath);
            $disk->delete($existingPath);
        } else {
            // Save as new file
            $directory = $newPath ?? dirname($existingPath);
            $optimizedPath = $directory . '/' . Str::uuid() . '.webp';
        }

        $disk->put($optimizedPath, (string) $encoded);

        return $optimizedPath;
    }
}
