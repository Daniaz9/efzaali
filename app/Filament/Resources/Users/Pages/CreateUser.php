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
        $role = $this->getUserRoleForThisPage();

        $photo = $data['photo'] ?? null;
        unset($data['photo']);

        // Create the user (without photo)
        $user = static::getModel()::create($data);
        $user->assignRole($role);


        // Handle the photo upload if exists
        if ($photo instanceof TemporaryUploadedFile) {
            $path = $photo->store('photos/users', 'public');
//            $this->createSmallImage($path);
//            $user->photo()->create(['path' => $path]);
        }

        if ($photo!=null){
            $this->createSmallImage($photo);

            $user->photo()->create(['path' => $photo]);
        }


        return $user;
    }

    protected function getUserRoleForThisPage(): string
    {
        $pageClass = $this->previousUrl ?? null;

        if (str_contains($pageClass, 'customers')) {
            return 'customer';
        }

        if (str_contains($pageClass, 'drivers')) {
            return 'driver';
        }

        if (str_contains($pageClass, 'admins')) {
            return 'admin';
        }

        return 'customer'; // fallback
    }
}
