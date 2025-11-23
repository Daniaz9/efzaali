<?php

namespace App\Filament\Resources\Stores\Pages;

use App\Filament\Resources\Stores\StoreResource;
use App\Models\Store;
use App\Traits\HandlesImages;
use Filament\Resources\Pages\CreateRecord;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class CreateStore extends CreateRecord
{
    use HandlesImages;

    protected static string $resource = StoreResource::class;

    protected function handleRecordCreation(array $data): \App\Models\Store
    {
        $photo = $data['photo'] ?? null;
        unset($data['photo']);

        $store = Store::create($data);

        if ($photo instanceof TemporaryUploadedFile) {
            $path = $photo->store('photos/stores', 'public');

        }
        if($photo!=null){
            $this->createSmallImage($photo);

            $store->photo()->create(['path' => $photo]);
        }


        return $store;
    }

}
