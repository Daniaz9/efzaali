<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;


class UserRegistrationsChart extends ChartWidget
{
    protected  ?string $heading = 'User Registration Chart';
    protected  ?string $description = 'New users per day';
    protected  ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $data = $this->getUserRegistrationsPerDay();

        return [
            'datasets' => [
                [
                    'label' => 'User Registrations',
                    'data' => $data->pluck('count'),
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'fill' => true,
                    'tension' => 0.4,
                ],
            ],
            'labels' => $data->pluck('date')->map(fn ($date) => Carbon::parse($date)->format('M j')),
        ];
    }

    protected function getUserRegistrationsPerDay()
    {
        return User::query()
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    protected function getType(): string
    {
        return 'line';
    }
}
