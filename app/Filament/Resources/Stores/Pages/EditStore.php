<?php

namespace App\Filament\Resources\Stores\Pages;

use App\Filament\Resources\Stores\StoreResource;
use App\Traits\HandlesImages;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class EditStore extends EditRecord
{
    protected static string $resource = StoreResource::class;

    use HandlesImages;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
    protected function handleRecordUpdate($record, array $data): \Illuminate\Database\Eloquent\Model
    {
        $photo = $data['photo'] ?? null;
        unset($data['photo']);

        $record->update($data);

//        if ($photo instanceof TemporaryUploadedFile) {
            // Delete old one
            if ($record->photo) {
                $this->deleteImageAndSmall($record->photo->path);
                $record->photo->delete();
            }

//            $path = $photo->store('photos/stores', 'public');
            $this->createSmallImage($photo);

            $record->photo()->create(['path' => $photo]);
//        }

        return $record;
    }
}
