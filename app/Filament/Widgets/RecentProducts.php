<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class RecentProducts extends TableWidget
{
    protected int | string | array $columnSpan = 'full';
    protected static ?int $sort = 1; // This makes it first


    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => Product::query())
            ->columns([
                ImageColumn::make('photos.path')
                    ->label('')
                    ->stacked() // stack them like overlapping circles
                    ->limit(3)
                    ->limitedRemainingText()
                    ->circular()
                    ->height(40)
                    ->width(40)
                    ->tooltip(fn($record) => $record->photos->count() . ' photos available'),

                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->limit(50)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 50) {
                            return null;
                        }
                        return $state;
                    }),

                TextColumn::make('store.name')
                    ->label('Store')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('price')
                    ->money('USD')
                    ->sortable(),
                TextColumn::make('stock')
                    ->badge()
                    ->color(fn (int $state): string => match (true) {
                        $state === 0 => 'danger',
                        $state < 5 => 'warning',
                        $state < 10 => 'primary',
                        default => 'success',
                    })
                    ->sortable(),

//                TextColumn::make('status')
//                    ->badge()
//                    ->color(fn (string $state): string => match ($state) {
//                        'active' => 'success',
//                        'inactive' => 'danger',
//                        'draft' => 'warning',
//                        default => 'gray',
//                    })
//                    ->sortable(),

                TextColumn::make('created_at')
                    ->dateTime('M j, Y g:i A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

            ])
            ->filters([
                //
            ])
            ->headerActions([
                //
            ])
            ->recordActions([
                EditAction::make()
                    ->url(fn (Product $record) => route('filament.admin.resources.products.edit', $record)),

            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    //
                ]),
            ]);
    }
    public static function canView(): bool
    {
        return Product::count() > 0;
    }
}
