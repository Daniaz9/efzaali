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

            Stat::make('Low Stock Products', Product::where('stock', '<', 10)->count())
                ->description('Need immediate attention')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('danger')
                ->url(route('filament.admin.resources.products.index', [
                    'tableFilters' => [
                        'low_stock' => ['value' => true]
                    ]
                ])),

            // Total Orders
            Stat::make('Total Orders', Order::count() ?? 0)
                ->description('All time orders')
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->color('primary')
                ->chart($this->getOrderGrowthChart()),
//                ->url(route('filament.admin.resources.orders.index')), // Adjust if you have orders

            //Total drivers
            Stat::make('Total Drivers', User::role('driver')->count())
                ->description('Registered drivers')
                ->descriptionIcon('heroicon-m-truck')
                ->color('info')
                ->chart($this->getDriverGrowthChart())
                ->url(route('filament.admin.resources.users.index', [
                    'tableFilters' => [
                        'role' => ['value' => 'driver']
                    ]
                ])),

            //Total customers
            Stat::make('Total Customers', User::role('customer')->count())
                ->description('Registered customers')
                ->descriptionIcon('heroicon-m-users')
                ->color('info')
                ->chart($this->getDriverGrowthChart())
                ->url(route('filament.admin.resources.users.index', [
                    'tableFilters' => [
                        'role' => ['value' => 'customer']
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

    protected function getDriverGrowthChart(): array
    {
        // Last 7 days driver growth
        return User::role('driver')
            ->where('created_at', '>=', now()->subDays(7))
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('count')
            ->toArray();
    }

    protected function getCustomerGrowthChart(): array
    {
        // Last 7 days customer growth
        return User::role('customer')
            ->where('created_at', '>=', now()->subDays(7))
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('count')
            ->toArray();
    }

    protected function getHeading(): ?string
    {
        return 'Analytics';
    }

    protected function getOrderGrowthChart(): array
    {
        // Last 7 days order growth
        if (class_exists(Order::class)) {
            return Order::where('created_at', '>=', now()->subDays(7))
                ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->groupBy('date')
                ->orderBy('date')
                ->get()
                ->pluck('count')
                ->toArray();
        }

        return [0, 0, 0, 0, 0, 0, 0];
    }
}

