<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Traits\HandlesImages;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class EditUser extends EditRecord
{
    use HandlesImages;

    protected static string $resource = UserResource::class;

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

        // Update user info
        $record->update($data);
        if (!empty($photo)) {
            // Delete old photo + small version
            if ($record->photo) {
                $this->deleteImageAndSmall($record->photo->path);
                $record->photo->delete();
            }

            // Store new one
            $this->createSmallImage($photo);
            $record->photo()->create(['path' => $photo]);
        }

        return $record;
    }
}
