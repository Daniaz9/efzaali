<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Models\User;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class Admins extends ListRecords
{
    protected static string $resource = UserResource::class;

//    protected static string $view = 'filament.resources.user-resource.pages.admins';

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->role(['admin', 'super_admin']))
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('phone_number')
                    ->searchable(),
                TextColumn::make('roles.name')
                    ->badge(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Create Admin')
                    ->using(function (array $data) {
                        $user = User::create($data);
                        $user->assignRole('admin');
                        return $user;
                    }),
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
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
