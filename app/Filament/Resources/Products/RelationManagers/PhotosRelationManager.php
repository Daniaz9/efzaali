<?php

namespace App\Filament\Resources\Products\RelationManagers;

use App\Traits\HandlesImages;
use Filament\Actions\Action;
use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class PhotosRelationManager extends RelationManager
{
    use HandlesImages;
    protected static string $relationship = 'photos';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                FileUpload::make('path')
                    ->label('photos')
                    ->image()
                    ->directory('photos/products')
                    ->disk('public')
                    ->visibility('public')
                    ->previewable()
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('path')
            ->columns([
                ImageColumn::make('path')
                    ->label('Photo')
                    ->height(100),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        return $this->processSingleUploadedFile($data);
                    }),
                Action::make('uploadMultiple')
                    ->form([
                        FileUpload::make('photos')
                            ->label('Multiple Photos')
                            ->multiple()
                            ->image()
                            ->directory('photos/products')
                            ->disk('public')
                            ->visibility('public')
                            ->required()
                            ->maxFiles(10)
                            ->maxSize(5120)
                    ])
                    ->action(function (array $data): void {
                        $files = $data['photos'] ?? [];

                        foreach ($files as $file) {
                            if ($file instanceof TemporaryUploadedFile) {
                                $path = $file->store('photos/products', 'public');

                                // Create small copy

                            }
                            $this->createSmallImage($file);

                            // Create record in database
                            $this->getOwnerRecord()->photos()->create([
                                'path' => $file,
                            ]);
                        }
                    })
                    ->icon('heroicon-o-photo')
                    ->label('Add Multiple Photos'),
//                AssociateAction::make(),
            ])
            ->recordActions([
                EditAction::make()
                    ->mutateFormDataUsing(function (array $data, Model $record): array {
                        // For edit, we're only updating one photo at a time
                        if (isset($data['path']) ) {
                            $this->deleteImageAndSmall($record->path);
                            return $this->processSingleUploadedFile($data);
                        }
                        return $data;
                    }),
//                DissociateAction::make(),
                DeleteAction::make()
                    ->action(function (Model $record) {
                        $relativePath=$this->extractRelativePath($record->path);
                        // Delete files from storage
                        $this->deleteImageAndSmall($relativePath);
                        // Then delete from database
                        $record->delete();
                    })
                    ->modalHeading('Delete Photo')
                    ->modalSubheading(function (Model $record) {
                        return 'Are you sure you want to delete ' . basename($record->path) . '?';
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
//                    DissociateBulkAction::make(),
                    DeleteBulkAction::make()
                        ->before(function ($records) {
                            foreach ($records as $record) {
                                $relativePath=$this->extractRelativePath($record->path);
                                $this->deleteImageAndSmall($relativePath);
                            }
                        }),
                ]),
            ]);
    }

    protected function processSingleUploadedFile(array $data): array
    {
//        if (isset($data['path']) && $data['path'] instanceof TemporaryUploadedFile) {
//            $file = $data['path'];
//            $path = $file->store('photos/products', 'public');
//
//            // Create small copy
//            $this->createSmallImage($path);
//
//            $data['path'] = $path;
//        }
        $data['path'] = $this->extractRelativePath($data['path']);
        $path = $data['path'];

        $this->createSmallImage($path);

        return $data;
    }

    /**
     * Process multiple file uploads (if you need it elsewhere)
     */
//    protected function processMultipleUploadedFiles(array $files): array
//    {
//        $paths = [];
//
//        foreach ($files as $file) {
//            if ($file instanceof TemporaryUploadedFile) {
//                $path = $file->store('photos/products', 'public');
//
//                // Create small copy
//                $this->createSmallImage($path);
//
//                $paths[] = $path;
//            }
//        }
//
//        return $paths;
//    }
    protected function extractRelativePath(string $urlOrPath): string
    {
        // If it's already a relative path, return as is
        if (!str_contains($urlOrPath, 'http')) {
            return $urlOrPath;
        }

        // Extract the path after /storage/
        $pattern = '/\/storage\/(.+)/';
        if (preg_match($pattern, $urlOrPath, $matches)) {
            return $matches[1];
        }

        return $urlOrPath;
    }
}
