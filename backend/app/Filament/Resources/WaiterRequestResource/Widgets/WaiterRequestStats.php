<?php

namespace App\Filament\Resources\WaiterRequestResource\Widgets;

use App\Models\WaiterRequest;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class WaiterRequestStats extends BaseWidget
{
    protected static ?int $sort = 1;
    
    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        $pending = WaiterRequest::where('status', 'pending')->count();
        $acknowledged = WaiterRequest::where('status', 'acknowledged')->count();
        $completedToday = WaiterRequest::where('status', 'completed')
            ->whereDate('completed_at', today())
            ->count();
            
        // Calculate average response time for today
        $avgResponseTime = WaiterRequest::whereNotNull('acknowledged_at')
            ->whereDate('created_at', today())
            ->get()
            ->avg(function ($request) {
                return $request->created_at->diffInSeconds($request->acknowledged_at);
            });

        return [
            Stat::make(__('waiter_requests.widgets.stats.pending'), $pending)
                ->description(__('waiter_requests.widgets.stats.pending_description'))
                ->descriptionIcon('heroicon-o-clock')
                ->color($pending > 5 ? 'danger' : ($pending > 0 ? 'warning' : 'success'))
                ->chart($this->getPendingChart()),
                
            Stat::make(__('waiter_requests.widgets.stats.in_progress'), $acknowledged)
                ->description(__('waiter_requests.widgets.stats.in_progress_description'))
                ->descriptionIcon('heroicon-o-eye')
                ->color('warning'),
                
            Stat::make(__('waiter_requests.widgets.stats.completed_today'), $completedToday)
                ->description(__('waiter_requests.widgets.stats.completed_description'))
                ->descriptionIcon('heroicon-o-check-circle')
                ->color('success'),
                
            Stat::make(__('waiter_requests.widgets.stats.avg_response_time'), $avgResponseTime ? round($avgResponseTime) . 's' : '—')
                ->description(__('waiter_requests.widgets.stats.avg_response_description'))
                ->descriptionIcon('heroicon-o-clock')
                ->color($avgResponseTime && $avgResponseTime <= 30 ? 'success' : 'warning'),
        ];
    }

    protected function getPendingChart(): array
    {
        // Get last 7 days of pending requests
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = today()->subDays($i);
            $count = WaiterRequest::whereDate('created_at', $date)
                ->where('status', 'pending')
                ->count();
            $data[] = $count;
        }
        return $data;
    }

    protected function getPollingInterval(): ?string
    {
        return '5s'; // Refresh every 5 seconds
    }
}
