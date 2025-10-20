<?php

namespace App\Filament\Resources\Orders\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('customer.name')
                    ->label('customer')
                    ->searchable(),
                TextColumn::make('driver.name')
                    ->name('driver')
                    ->searchable(),
                TextColumn::make('type')
                    ->badge()
                    ->searchable(),
                TextColumn::make('delivery_type')
                    ->searchable(),
                TextColumn::make('status')
                    ->badge()
                    ->searchable(),
                TextColumn::make('pickup_address')
                    ->searchable(),
                TextColumn::make('dropoff_address')
                    ->searchable(),
//                TextColumn::make('pickup_lat')
//                    ->numeric()
//                    ->sortable(),
//                TextColumn::make('pickup_long')
//                    ->numeric()
//                    ->sortable(),
//                TextColumn::make('dropoff_lat')
//                    ->numeric()
//                    ->sortable(),
//                TextColumn::make('dropoff_long')
//                    ->numeric()
//                    ->sortable(),
                TextColumn::make('distance')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('delivery_fee')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('total_price')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('driver_assigned_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('picked_up_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('on_the_way_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('delivered_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('cancelled_at')
                    ->dateTime()
                    ->sortable(),
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
            ->recordActions([
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
