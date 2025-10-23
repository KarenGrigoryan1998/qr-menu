<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class OrderStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        // Today's orders
        $todayOrders = Order::whereDate('created_at', today())->count();
        
        // Yesterday's orders for comparison
        $yesterdayOrders = Order::whereDate('created_at', today()->subDay())->count();
        $ordersTrend = $yesterdayOrders > 0 
            ? round((($todayOrders - $yesterdayOrders) / $yesterdayOrders) * 100, 1)
            : 0;
        
        // Today's revenue
        $todayRevenue = Order::whereDate('created_at', today())
            ->where('payment_status', 'paid')
            ->sum('total_amount');
            
        // Yesterday's revenue for comparison
        $yesterdayRevenue = Order::whereDate('created_at', today()->subDay())
            ->where('payment_status', 'paid')
            ->sum('total_amount');
        $revenueTrend = $yesterdayRevenue > 0
            ? round((($todayRevenue - $yesterdayRevenue) / $yesterdayRevenue) * 100, 1)
            : 0;
        
        // Active orders (not completed or closed)
        $activeOrders = Order::whereNotIn('status', ['closed'])
            ->where('payment_status', 'unpaid')
            ->count();
        
        // Average order value today
        $avgOrderValue = Order::whereDate('created_at', today())
            ->where('payment_status', 'paid')
            ->avg('total_amount');

        return [
            Stat::make(__('Today\'s Orders'), $todayOrders)
                ->description($ordersTrend >= 0 ? "+{$ordersTrend}% from yesterday" : "{$ordersTrend}% from yesterday")
                ->descriptionIcon($ordersTrend >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($ordersTrend >= 0 ? 'success' : 'danger')
                ->chart($this->getOrdersChart()),
                
            Stat::make(__('Today\'s Revenue'), number_format($todayRevenue, 0) . ' ֏')
                ->description($revenueTrend >= 0 ? "+{$revenueTrend}% from yesterday" : "{$revenueTrend}% from yesterday")
                ->descriptionIcon($revenueTrend >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($revenueTrend >= 0 ? 'success' : 'danger')
                ->chart($this->getRevenueChart()),
                
            Stat::make(__('Active Orders'), $activeOrders)
                ->description('Orders in progress')
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->color($activeOrders > 10 ? 'warning' : 'success'),
                
            Stat::make(__('Avg Order Value'), number_format($avgOrderValue ?? 0, 0) . ' ֏')
                ->description('Average per order today')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('info'),
        ];
    }

    protected function getOrdersChart(): array
    {
        // Get last 7 days of orders
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = today()->subDays($i);
            $count = Order::whereDate('created_at', $date)->count();
            $data[] = $count;
        }
        return $data;
    }

    protected function getRevenueChart(): array
    {
        // Get last 7 days of revenue
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = today()->subDays($i);
            $revenue = Order::whereDate('created_at', $date)
                ->where('payment_status', 'paid')
                ->sum('total_amount');
            $data[] = $revenue / 1000; // Scale down for chart
        }
        return $data;
    }

    protected function getPollingInterval(): ?string
    {
        return '30s'; // Refresh every 30 seconds
    }
}
