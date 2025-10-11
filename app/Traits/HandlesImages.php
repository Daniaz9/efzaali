<?php

namespace App\Traits;

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\Storage;

trait HandlesImages
{
    /**
     * Create a small (thumbnail) copy of the image.
     * Input $path is the relative path stored in DB (e.g. "photos/products/abc.jpg").
     * Returns the small image relative path (e.g. "photos/products/abc-small.jpg").
     */
    protected function createSmallImage(string $path): string
    {
        $fullPath = Storage::disk('public')->path($path);

        $extension = pathinfo($path, PATHINFO_EXTENSION);
        $filename  = basename($path, '.' . $extension);
        $smallPath = str_replace(basename($path), "{$filename}-small.{$extension}", $path);

        // Use Intervention ImageManager (v3 syntax)
        $manager = new ImageManager(new Driver());
        $image = $manager->read($fullPath)->scaleDown(300, 300);

        Storage::disk('public')->put($smallPath, (string) $image->encode());

        return $smallPath;
    }

    /**
     * Delete an image and its small version (if exist).
     * Accepts the DB-stored relative path.
     */
    protected function deleteImageAndSmall(?string $path): void
    {
        if (! $path) return;

        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }

        $small = $this->getSmallImagePath($path);
        if (Storage::disk('public')->exists($small)) {
            Storage::disk('public')->delete($small);
        }
    }

    /**
     * Build the small image relative path from the main path.
     */
    protected function getSmallImagePath(string $path): string
    {
        $extension = pathinfo($path, PATHINFO_EXTENSION);
        $filename  = basename($path, '.' . $extension);
        return str_replace(basename($path), "{$filename}-small.{$extension}", $path);
    }
}
