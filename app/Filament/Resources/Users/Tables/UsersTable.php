<?php

namespace App\Filament\Resources\Users\Tables;

use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('photo.path')
                    ->label('')
                    ->circular()
                    ->height(40)
                    ->width(40)
                    ->disk('public'),
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Email address')
                    ->searchable(),
                TextColumn::make('roles.name')
                    ->badge(),
                IconColumn::make('is_available')
                    ->boolean(),
                TextColumn::make('email_verified_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('phone_number')
                    ->searchable(),
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
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->actions([
                Action::make('changeRole')
                    ->label('Change Role')
                    ->icon('heroicon-m-arrows-right-left')
                    ->form([
                        Select::make('role')
                            ->options([
                                'driver' => 'Driver',
                                'normal-user' => 'Customer',
                                'admin'=>'Admin',
                                'super-admin'=>'Super Admin',
                            ])
                            ->required(),
                    ])
                    ->action(function (User $record, array $data) {
                        $record->syncRoles([$data['role']]);
                    }),
                EditAction::make(),
//                DeleteAction::make(),
            ]);
        ;
    }
}
