<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends StatsOverviewWidget
{
    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {

        return [
            //Total Stores
            Stat::make('Total Stores', Store::count())
                ->description('All registered stores')
                ->descriptionIcon('heroicon-m-building-storefront')
                ->color('success')
                ->chart($this->getStoreGrowthChart())
                ->url(route('filament.admin.resources.stores.index')),
            // Total Products
            Stat::make('Total Products', Product::count())
                ->description('Active products in system')
                ->descriptionIcon('heroicon-m-cube')
                ->color('warning')
                ->chart($this->getProductGrowthChart())
                ->url(route('filament.admin.resources.products.index')),

            // Total Users
            Stat::make('Total Users', User::count())
                ->description('Registered users')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('info')
                ->chart($this->getUserGrowthChart())
                ->url(route('filament.admin.resources.users.index')),

            // Total Orders (if you have Order model)
//            Stat::make('Total Orders', Order::count() ?? 0)
//                ->description('All time orders')
//                ->descriptionIcon('heroicon-m-shopping-bag')
//                ->color('primary')
////                ->chart($this->getOrderGrowthChart())
//                ->url(route('filament.admin.resources.orders.index')), // Adjust if you have orders
            Stat::make('Low Stock Products', Product::where('stock', '<', 10)->count())
                ->description('Need immediate attention')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('danger')
                ->url(route('filament.admin.resources.products.index', [
                    'tableFilters' => [
                        'low_stock' => ['value' => true]
                    ]
                ])),
        ];
    }
    protected function getStoreGrowthChart(): array
    {
        // Last 7 days store growth
        return Store::where('created_at', '>=', now()->subDays(7))
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('count')
            ->toArray();
    }

    protected function getProductGrowthChart(): array
    {
        // Last 7 days product growth
        return Product::where('created_at', '>=', now()->subDays(7))
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('count')
            ->toArray();
    }

    protected function getUserGrowthChart(): array
    {
        // Last 7 days user growth
        return User::where('created_at', '>=', now()->subDays(7))
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('count')
            ->toArray();
    }
}

