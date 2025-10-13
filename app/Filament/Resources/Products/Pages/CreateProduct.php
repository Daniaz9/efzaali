<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\ProductResource;
use App\Models\Product;
use Filament\Resources\Pages\CreateRecord;
use App\Traits\HandlesImages;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;


class CreateProduct extends CreateRecord
{
    use HandlesImages;

    protected static string $resource = ProductResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function handleRecordCreation(array $data): Product
    {
        // 1️⃣ Create product (without photos)
        $product = Product::create(collect($data)->except('photos')->toArray());

        // 2️⃣ Handle photos
        if (!empty($data['photos'])) {
            foreach ($data['photos'] as $file) {
                if ($file instanceof TemporaryUploadedFile) {
                    // Filament Livewire temp file → move it
                    $path = $file->store('photos/products', 'public');
                } elseif (is_string($file)) {
                    // Already a path string
                    $path = $file;
                } else {
                    // Skip invalid entries
                    continue;
                }

                // ✅ Now $path is guaranteed to be a string
                if (Storage::disk('public')->exists($path)) {
                    $this->createSmallImage($path);
                    $product->photos()->create(['path' => $path]);
                } else {
                    Log::warning("Missing file after upload: {$path}");
                }
            }
        }

        return $product;
    }
}
