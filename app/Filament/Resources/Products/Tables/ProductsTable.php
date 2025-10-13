<?php

namespace App\Filament\Resources\Products\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                ImageColumn::make('photos.path')
                    ->label('Photos')
                    ->stacked() // stack them like overlapping circles
                    ->limit(3)
                    ->limitedRemainingText()
                    ->circular()
                    ->height(40)
                    ->width(40)
                    ->tooltip(fn($record) => $record->photos->count() . ' photos available'),

                TextColumn::make('store.name')
                    ->sortable(),
                TextColumn::make('price')
                    ->money()
                    ->sortable()
                    ->searchable(),
                TextColumn::make('stock')
                    ->numeric()
                    ->sortable()
                    ->searchable(),
                TextColumn::make('description')
                    ->searchable()
                    ->limit(50)
                    ->wrap()
                    ->tooltip(fn ($record) => $record->description),
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
                SelectFilter::make('store')
                ->relationship('store','name'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
