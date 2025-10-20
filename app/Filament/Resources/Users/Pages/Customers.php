<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Models\User;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class Customers extends ListRecords
{
    protected static string $resource = UserResource::class;

//    protected static string $view = 'filament.resources.user-resource.pages.customers';

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->role('normal-user'))
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('phone_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Create Customer')
                    ->using(function (array $data) {
                        $user = User::create($data);
                        $user->assignRole('customer');
                        return $user;
                    }),
            ])
            ->actions([
                Action::make('changeRole')
                    ->label('Change Role')
                    ->icon('heroicon-m-arrows-right-left')
                    ->form([
                        \Filament\Forms\Components\Select::make('role')
                            ->options([
                                'driver' => 'Driver',
                                'admin' => 'Admin',
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
