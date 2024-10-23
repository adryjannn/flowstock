<?php
namespace App\Filament\Widgets;

use App\Models\ShopOrder;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ShopOrderStats extends BaseWidget
{
    protected static ?int $sort = 1;
    protected static ?string $pollingInterval = '20s';

    protected function getStats(): array
    {
        $now = Carbon::now();
        $currentMonth = $now->month;
        $currentYear = $now->year;
        $previousMonth = $now->copy()->subMonth()->month;
        $previousYear = $now->copy()->subMonth()->year;

        // Current month statistics
        $currentMonthOrdersCount = ShopOrder::whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->count();
        $currentMonthRevenue = ShopOrder::whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->sum('total_paid');
        $todayOrdersCount = ShopOrder::whereDate('created_at', $now->toDateString())->count();

        // Previous month statistics
        $previousMonthOrdersCount = ShopOrder::whereMonth('created_at', $previousMonth)
            ->whereYear('created_at', $previousYear)
            ->count();
        $previousMonthRevenue = ShopOrder::whereMonth('created_at', $previousMonth)
            ->whereYear('created_at', $previousYear)
            ->sum('total_paid');

        // Calculate changes
        $ordersChange = $this->calculateChange($currentMonthOrdersCount, $previousMonthOrdersCount);
        $revenueChange = $this->calculateChange($currentMonthRevenue, $previousMonthRevenue);

        // Data for charts
        $ordersPerMonthData = ShopOrder::selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, COUNT(*) as count')
            ->whereYear('created_at', $currentYear)
            ->groupBy('year', 'month')

            ->get()
            ->mapWithKeys(function ($order) {
                return [sprintf('%d-%02d', $order->year, $order->month) => $order->count];
            })
            ->toArray();

        $ordersPerDayCurrentMonthData = ShopOrder::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->groupBy('date')

            ->get()
            ->mapWithKeys(function ($order) {
                return [$order->date => $order->count];
            })
            ->toArray();

        $revenuePerDayData = ShopOrder::selectRaw('DATE(created_at) as date, SUM(total_paid) as revenue')
            ->groupBy('date')

            ->get()
            ->mapWithKeys(function ($order) {
                return [$order->date => $order->revenue];
            })
            ->toArray();

        // Prepare data for charts
        $months = array_keys($ordersPerMonthData);
        $ordersData = array_values($ordersPerMonthData);

        $dates = array_keys($revenuePerDayData);
        $revenueData = array_values($revenuePerDayData);

        $currentMonthDates = array_keys($ordersPerDayCurrentMonthData);
        $currentMonthOrdersData = array_values($ordersPerDayCurrentMonthData);

        return [
            Stat::make(__('Total Orders (This Month)'), $currentMonthOrdersCount)
                ->description(__('Total number of orders this month'))
                ->descriptionIcon($ordersChange['icon'])
                ->color($ordersChange['color'])
                ->chart($ordersData)
                ->description($ordersChange['text']),

            Stat::make(__('Total Revenue (This Month)'), 'PLN ' . number_format($currentMonthRevenue, 2))
                ->description(__('Total revenue generated this month'))
                ->descriptionIcon($revenueChange['icon'])
                ->color($revenueChange['color'])
                ->chart($revenueData)
                ->description($revenueChange['text']),

            Stat::make(__('Orders Today'), $todayOrdersCount)
                ->description(__('Number of orders today'))
                ->descriptionIcon('heroicon-o-calendar')
                ->color('info')
                ->chart($currentMonthOrdersData),
        ];
    }

    private function calculateChange($current, $previous): array
    {
        if ($previous == 0) {
            return ['icon' => 'heroicon-m-arrow-trending-up', 'text' => __('No previous data'), 'color' => 'gray'];
        }

        $change = (($current - $previous) / $previous) * 100;
        if ($change > 0) {
            return ['icon' => 'heroicon-m-arrow-trending-up', 'text' => sprintf(__('Increase of %.2f%%'), $change), 'color' => 'success'];
        } elseif ($change < 0) {
            return ['icon' => 'heroicon-m-arrow-trending-down', 'text' => sprintf(__('Decrease of %.2f%%'), abs($change)), 'color' => 'danger'];
        } else {
            return ['icon' => 'heroicon-o-arrow-right', 'text' => __('No change'), 'color' => 'gray'];
        }
    }
}
