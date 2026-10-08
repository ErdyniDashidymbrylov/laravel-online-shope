<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\DailySalesReport;
use App\Models\Order;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Collection;

class SalesReportService
{
    private const REPORT_DAYS = 7;

    private const SUCCESSFUL_ORDER_STATUSES = [
        Order::STATUS_PAID,
        Order::STATUS_SHIPPED,
        Order::STATUS_COMPLETED,
    ];

    public function refreshRecentReports(): void
    {
        $today = Carbon::today();

        for ($daysAgo = 0; $daysAgo < self::REPORT_DAYS; $daysAgo++) {
            $this->refreshReportForDate($today->copy()->subDays($daysAgo));
        }
    }

    public function refreshReportForDate(CarbonInterface $date): DailySalesReport
    {
        $orders = Order::query()
            ->whereBetween('created_at', [
                $date->copy()->startOfDay(),
                $date->copy()->endOfDay(),
            ]);

        $successfulOrders = (clone $orders)->whereIn('status', self::SUCCESSFUL_ORDER_STATUSES);

        return DailySalesReport::query()->updateOrCreate(
            ['report_date' => $date->toDateString()],
            [
                'orders_count' => (clone $orders)->count(),
                'sales_count' => (clone $successfulOrders)->count(),
                'revenue' => (clone $successfulOrders)->sum('total'),
                'canceled_count' => (clone $orders)->where('status', Order::STATUS_CANCELED)->count(),
                'calculated_at' => now(),
            ],
        );
    }

    /**
     * @return Collection<int, DailySalesReport>
     */
    public function getRecentReports(): Collection
    {
        return DailySalesReport::query()
            ->whereBetween('report_date', [
                Carbon::today()->subDays(self::REPORT_DAYS - 1)->toDateString(),
                Carbon::today()->toDateString(),
            ])
            ->orderBy('report_date')
            ->get();
    }

    /**
     * @return array<string, mixed>
     */
    public function getCurrentWeekReport(): array
    {
        $today = Carbon::now();

        return $this->getReport($today->copy()->startOfWeek(), $today->copy()->endOfWeek());
    }

    /**
     * @return array<string, mixed>
     */
    public function getReport(Carbon $startDate, Carbon $endDate): array
    {
        $orders = Order::query()
            ->whereIn('status', self::SUCCESSFUL_ORDER_STATUSES)
            ->whereBetween('created_at', [
                $startDate->copy()->startOfDay(),
                $endDate->copy()->endOfDay(),
            ])
            ->get(['created_at', 'total']);

        $totalSales = $orders->count();
        $totalRevenue = (float) $orders->sum('total');

        return [
            'period' => [
                'from' => $startDate->toDateString(),
                'to' => $endDate->toDateString(),
            ],
            'total_sales' => $totalSales,
            'total_revenue' => $totalRevenue,
            'average_check' => $totalSales > 0 ? round($totalRevenue / $totalSales, 2) : 0.0,
            'daily_sales' => $this->getDailySales($orders, $startDate, $endDate),
        ];
    }

    /**
     * @param Collection<int, Order> $orders
     * @return array<int, array{date: string, sales_count: int, revenue: float}>
     */
    private function getDailySales(Collection $orders, Carbon $startDate, Carbon $endDate): array
    {
        $result = [];

        foreach (CarbonPeriod::create($startDate->copy()->startOfDay(), $endDate->copy()->startOfDay()) as $date) {
            $dateString = $date->toDateString();
            $dayOrders = $orders->filter(
                fn (Order $order): bool => $order->created_at->toDateString() === $dateString,
            );

            $result[] = [
                'date' => $dateString,
                'sales_count' => $dayOrders->count(),
                'revenue' => (float) $dayOrders->sum('total'),
            ];
        }

        return $result;
    }
}
