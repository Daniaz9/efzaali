<?php

namespace App\Filament\Resources\Products\Schemas;

use App\Traits\HandlesImages;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;


class ProductForm
{
    use HandlesImages;
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('store_id')
                    ->label('Store')
                    ->relationship('store', 'name')
                    ->required(),
                TextInput::make('name')
                    ->required(),
                Textarea::make('description')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->prefix('$'),
                TextInput::make('stock')
                    ->required()
                    ->numeric(),
                        FileUpload::make('photos')
                            ->label('Upload Product Photos')
                            ->multiple()
                            ->image()
                            ->reorderable()
                            ->directory('photos/products')
                            ->preserveFilenames()
                            ->downloadable()
                            ->previewable(true)
                            ->getUploadedFileNameForStorageUsing(fn (TemporaryUploadedFile $file): string =>
                                str()->random(20) . '.' . $file->getClientOriginalExtension()
                            )
                            ->saveRelationshipsUsing(function ($component, $state, $record) {
                                // Delete existing photos (optional if you want to replace all)
                                $record->photos()->each(function ($photo) {
                                    // Delete from storage + small version
                                    (new static)->deleteImageAndSmall(str_replace(asset('storage/') , '', $photo->path));
                                    $photo->delete();
                                });

                                if (is_array($state)) {
                                    foreach ($state as $filePath) {
                                        // Store main photo
                                        $photo = $record->photos()->create(['path' => $filePath]);
                                        // Create small image
                                        (new static)->createSmallImage($filePath);
                                    }
                                }
                            }),
            ]);
    }
}
