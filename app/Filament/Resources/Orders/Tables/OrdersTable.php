<?php

namespace App\Filament\Resources\Orders\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('customer.name')
                    ->label('Customer')
                    ->searchable(),
                TextColumn::make('driver.name')
                    ->label('Driver')
                    ->searchable(),
                TextColumn::make('type')
                    ->label('Order type')
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
//                TextColumn::make('distance')
//                    ->numeric()
//                    ->sortable(),
                TextColumn::make('delivery_fee')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('total_price')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('description'),
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
//            ->actions([
//                ViewAction::make()
//                    ->label('View Order Details')
//                    ->icon('heroicon-o-document-text')
//                    ->color('gray')
//                    ->modalWidth('6xl')
//            ])
            ->recordActions([
                Action::make('viewProducts')
                    ->label('View Items')
                    ->icon('heroicon-o-shopping-bag')
                    ->color('gray')
                    ->modalHeading(function ($record) {
                        return "Order #{$record->id} - Items Details";
                    })
                    ->modalSubmitAction(false)
                    ->modalCancelAction(false)
                    ->slideOver()
                    ->form(function ($record) {
                        // Eager load relationships for better performance
                        $record->load(['products.store', 'customer', 'driver']);

                        return [
                            // Order Items
                            Section::make('Order Items')
                                ->schema([
                                    Repeater::make('products')
                                        ->relationship('products')
                                        ->label('Products')
                                        ->schema([
                                            Grid::make(2)
                                                ->schema([
                                                    // Product Info
                                                    Section::make('Product Information')
                                                        ->schema([
                                                            TextInput::make('name')
                                                                ->label('Product Name')
                                                                ->disabled()
                                                                ->columnSpanFull(),
                                                            Textarea::make('description')
                                                                ->label('Description')
                                                                ->disabled()
                                                                ->rows(2)
                                                                ->columnSpanFull(),
                                                            TextInput::make('store.name')
                                                                ->label('Store')
                                                                ->disabled(),
                                                        ]),

                                                    // Pricing & Quantity
                                                    Section::make('Pricing')
                                                        ->schema([
                                                            Grid::make(2)
                                                                ->schema([
                                                                    TextInput::make('price')
                                                                        ->label('Unit Price')
                                                                        ->prefix('$')
                                                                        ->disabled(),
                                                                    TextInput::make('pivot.quantity')
                                                                        ->label('Quantity')
                                                                        ->numeric()
                                                                        ->disabled(),
                                                                ]),
                                                            TextInput::make('pivot.total_price')
                                                                ->label('Item Total')
                                                                ->prefix('$')
                                                                ->disabled()
                                                                ->extraAttributes(['class' => 'font-bold text-green-600']),
                                                        ]),
                                                ]),
                                        ])
                                        ->columns(1)
                                        ->disableItemCreation()
                                        ->disableItemDeletion()
                                        ->disableItemMovement()
                                        ->itemLabel(fn (array $state): ?string => $state['name'] ?? null)
                                        ->defaultItems(0),
                                ]),

                            // Order Summary Footer
                            Section::make('Order Summary')
                                ->schema([
                                    Grid::make(2)
                                        ->schema([
                                            Placeholder::make('items_count')
                                                ->label('Total Items')
                                                ->content($record->products->sum('pivot.quantity')),
                                            Placeholder::make('products_count')
                                                ->label('Unique Products')
                                                ->content($record->products->count()),
                                            Placeholder::make('delivery_fee')
                                                ->label('Delivery Fee')
                                                ->content('$'.number_format($record->delivery_fee, 2)),
                                            Placeholder::make('final_total')
                                                ->label('Final Total')
                                                ->content('$'.number_format($record->total_price, 2)),
                                        ]),
                                ]),
                        ];
                    })

//                    ->form(
//                        function ($record) {
//                            return [];}
//                    )
//                Section::make('Order Items')
//                    ->schema([
//                        \Filament\Forms\Components\Repeatable::make('items')
//                            ->schema([
//                                \Filament\Forms\Components\Grid::make(4)
//                                    ->schema([
//                                        \Filament\Forms\Components\TextInput::make('name')
//                                            ->label('Product Name')
//                                            ->disabled(),
//                                        \Filament\Forms\Components\TextInput::make('quantity')
//                                            ->label('Qty')
//                                            ->disabled(),
//                                        \Filament\Forms\Components\TextInput::make('price')
//                                            ->label('Price')
//                                            ->disabled(),
//                                        \Filament\Forms\Components\TextInput::make('total_price')
//                                            ->label('Total')
//                                            ->disabled(),
//                                    ]),
//                                \Filament\Forms\Components\TextInput::make('store')
//                                    ->label('Store')
//                                    ->disabled(),
//                            ])
//                            ->default($record->products->map(function ($product) {
//                                return [
//                                    'name' => $product->name,
//                                    'quantity' => $product->pivot->quantity,
//                                    'price' => $product->price,
//                                    'total_price' => $product->pivot->total_price,
//                                    'store' => $product->store->name ?? 'N/A',
//                                ];
//                            })),
//                    ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
