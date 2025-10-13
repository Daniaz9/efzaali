<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\ProductResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function handleRecordUpdate($record, array $data): \Illuminate\Database\Eloquent\Model
    {
        // Update product details
        $record->update(collect($data)->except('photos')->toArray());

        // Handle new uploaded photos
        if (!empty($data['photos'])) {
            foreach ($data['photos'] as $file) {
                if ($file instanceof TemporaryUploadedFile) {
                    $path = $file->store('photos/products', 'public');
                    $this->createSmallImage($path);
                    $record->photos()->create(['path' => $path]);
                }
            }
        }

        return $record;
    }
}
