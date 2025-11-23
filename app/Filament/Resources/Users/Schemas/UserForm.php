<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->unique(ignoreRecord: true)
                    ->required(),
                Toggle::make('is_available')
                    ->required(),
                TextInput::make('password')
                    ->dehydrateStateUsing(fn ($state) => filled($state) ? bcrypt($state) : null) // hash only if not empty
                    ->dehydrated(fn ($state) => filled($state)) // don't send null if empty
                    ->required(fn (string $operation): bool => $operation === 'create') ,// only required on create
                TextInput::make('phone_number')
                    ->tel(),
                FileUpload::make('photo')
                    ->label('User Photo')
                    ->image()
                    ->disk('public')
                    ->directory('photos/avatars')
                    ->visibility('public')
                    ->previewable(true)
                    ->downloadable()
                    ->openable()
                    ->nullable()
                ]);
    }
}
