<?php

namespace App\Filament\Resources\Users;

use App\Filament\Resources\UserResource\Pages\Admins;
use App\Filament\Resources\UserResource\Pages\Customers;
use App\Filament\Resources\UserResource\Pages\Driverss;
use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Filament\Resources\Users\Schemas\UserForm;
use App\Filament\Resources\Users\Tables\UsersTable;
use App\Models\User;
use BackedEnum;
use Filament\Navigation\NavigationItem;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;


class UserResource extends Resource
{

    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static string | UnitEnum | null $navigationGroup = 'Users';


    public static function form(Schema $schema): Schema
    {
        return UserForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UsersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'edit' => EditUser::route('/{record}/edit'),
            'drivers' => Driverss::route('/driverss'),
            'customers' => Customers::route('/customers'),
            'admins' => Admins::route('/admins'),
        ];
    }

    public static function getNavigationItems(): array
    {
        return [
            NavigationItem::make(static::getNavigationLabel())
                ->group(static::getNavigationGroup())
                ->icon(static::getNavigationIcon())
                ->isActiveWhen(fn (): bool => request()->routeIs(static::getRouteBaseName() . '.index'))
                ->sort(static::getNavigationSort())
                ->badge(static::getNavigationBadge(), color: static::getNavigationBadgeColor()),

//            NavigationItem::make('All Users')
//                ->group(static::getNavigationGroup())
//                ->icon('heroicon-o-users')
//                ->isActiveWhen(fn (): bool => request()->routeIs(static::getRouteBaseName() . '.index'))
//                ->url(static::getUrl('index')),

            NavigationItem::make('Drivers')
                ->group(static::getNavigationGroup())
                ->icon('heroicon-o-truck')
                ->isActiveWhen(fn (): bool => request()->routeIs(static::getRouteBaseName() . '.drivers'))
                ->url(static::getUrl('drivers')),

            NavigationItem::make('Customers')
                ->group(static::getNavigationGroup())
                ->icon('heroicon-o-user-group')
                ->isActiveWhen(fn (): bool => request()->routeIs(static::getRouteBaseName() . '.customers'))
                ->url(static::getUrl('customers')),

            NavigationItem::make('Admins')
                ->group(static::getNavigationGroup())
                ->icon('heroicon-o-shield-check')
                ->isActiveWhen(fn (): bool => request()->routeIs(static::getRouteBaseName() . '.admins'))
                ->url(static::getUrl('admins')),
        ];
    }
}
