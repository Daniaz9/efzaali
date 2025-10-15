<?php

namespace App\Filament\Resources\Stores\Schemas;

use App\Models\Store;
use App\Traits\HandlesImages;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class StoreForm
{
    use HandlesImages;
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                Textarea::make('description')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('address')
                    ->required(),
                FileUpload::make('photo')
                    ->label('Store Photo')
                    ->image()
                    ->disk('public')
                    ->directory('photos/stores')
                    ->visibility('public')
                    ->previewable(true)
                    ->downloadable()
                    ->openable()
                    ->nullable()
                    ->maxSize(4096),
            ]);
    }
//    protected function handleRecordCreation(array $data): \App\Models\Store
//    {
//        $photo = $data['photo'] ?? null;
//        unset($data['photo']);
//
//        $store = Store::create($data);
//
//        if ($photo instanceof TemporaryUploadedFile) {
//            $path = $photo->store('photos/stores', 'public');
//
//        }
//        $this->createSmallImage($photo);
//
//        $store->photo()->create(['path' => $photo]);
//        return $store;
//    }


}
