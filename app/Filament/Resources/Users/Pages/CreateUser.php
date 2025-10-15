<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Traits\HandlesImages;
use Filament\Resources\Pages\CreateRecord;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class CreateUser extends CreateRecord
{
    use HandlesImages;

    protected static string $resource = UserResource::class;

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        $photo = $data['photo'] ?? null;
        unset($data['photo']);

        // Create the user (without photo)
        $user = static::getModel()::create($data);

        // Handle the photo upload if exists
        if ($photo instanceof TemporaryUploadedFile) {
            $path = $photo->store('photos/users', 'public');
            $this->createSmallImage($path);
            $user->photo()->create(['path' => $path]);
        }else{
            $path= $data['photo'];
            $this->createSmallImage($path);
            $user->photo()->create(['path' => $path]);
        }

        return $user;
    }
}
