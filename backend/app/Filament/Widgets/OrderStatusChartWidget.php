<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class OrderStatusChartWidget extends ChartWidget
{
    protected static ?string $heading = null;

    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = [
        'md' => 2,
        'xl' => 1,
    ];

    protected static ?string $maxHeight = '300px';

    public function getHeading(): string
    {
        return __('filament.widgets.order_status_distribution');
    }

    protected function getData(): array
    {
        // Get count by status for today
        $statuses = Order::whereDate('created_at', today())
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        return [
            'datasets' => [
                [
                    'label' => __('filament.widgets.orders_by_status'),
                    'data' => [
                        $statuses['pending'] ?? 0,
                        $statuses['paid'] ?? 0,
                        $statuses['cooking'] ?? 0,
                        $statuses['ready'] ?? 0,
                        $statuses['delivered'] ?? 0,
                        $statuses['closed'] ?? 0,
                    ],
                    'backgroundColor' => [
                        'rgb(251, 191, 36)',  // Yellow - Pending
                        'rgb(34, 197, 94)',   // Green - Paid
                        'rgb(249, 115, 22)',  // Orange - Cooking
                        'rgb(16, 185, 129)',  // Emerald - Ready
                        'rgb(168, 85, 247)',  // Purple - Delivered
                        'rgb(100, 116, 139)', // Slate - Closed
                    ],
                ],
            ],
            'labels' => [
                __('filament.statuses.pending'),
                __('filament.statuses.paid'),
                __('filament.statuses.cooking'),
                __('filament.statuses.ready'),
                __('filament.statuses.delivered'),
                __('filament.statuses.closed'),
            ],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getPollingInterval(): ?string
    {
        return '30s';
    }
}
