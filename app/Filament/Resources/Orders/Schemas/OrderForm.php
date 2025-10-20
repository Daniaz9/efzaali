<?php

namespace App\Filament\Resources\Orders\Schemas;

use App\Enums\DeliveryType;
use App\Enums\OrderStatus;
use App\Enums\OrderType;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('customer_id')
                    ->label('customer')
                    ->relationship('customer', 'name')
                    ->required(),
                Select::make('driver_id')
                    ->label('driver')
                    ->relationship('driver', 'name'),
                Select::make('type')
                    ->options(OrderType::class)
                    ->default('custom')
                    ->required(),
                Select::make('delivery_type')
                    ->options(DeliveryType::class)
                    ->required()
                    ->default('fast'),
                Select::make('status')
                    ->options(OrderStatus::class)
                    ->default('pending')
                    ->required(),
                TextInput::make('pickup_address')
                    ->required(),
                TextInput::make('dropoff_address')
                    ->required(),
//                TextInput::make('pickup_lat')
//                    ->required()
//                    ->numeric(),
//                TextInput::make('pickup_long')
//                    ->required()
//                    ->numeric(),
//                TextInput::make('dropoff_lat')
//                    ->required()
//                    ->numeric(),
//                TextInput::make('dropoff_long')
//                    ->required()
//                    ->numeric(),
                TextInput::make('distance')
                    ->required()
                    ->numeric(),
                TextInput::make('delivery_fee')
                    ->required()
                    ->numeric(),
                TextInput::make('total_price')
                    ->required()
                    ->numeric(),
                Textarea::make('description')
                    ->columnSpanFull(),
                DateTimePicker::make('driver_assigned_at'),
                DateTimePicker::make('picked_up_at'),
                DateTimePicker::make('on_the_way_at'),
                DateTimePicker::make('delivered_at'),
                DateTimePicker::make('cancelled_at'),
            ]);
    }
}
