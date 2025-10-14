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
        $product = Product::create(collect($data)->except('photos')->toArray());

        if (!empty($data['photos'])) {
            foreach ($data['photos'] as $file) {
                if ($file instanceof TemporaryUploadedFile) {
                    // New upload
                    $path = $file->store('photos/products', 'public');
                } elseif (is_string($file)) {
                    // Already stored (Filament sometimes does this automatically)
                    $path = $file;
                } else {
                    continue; // unknown type
                }

                if (Storage::disk('public')->exists($path)) {
                    $this->createSmallImage($path);

                    $product->photos()->create([
                        'path' => $path,
                        'imageable_id' => $product->id,
                        'imageable_type' => Product::class,
                    ]);
                }
            }
        }

        return $product;
    }
}
