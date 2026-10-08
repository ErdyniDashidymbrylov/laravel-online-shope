<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\DailySalesReport;
use App\Models\Order;
use App\Models\Role;
use App\Models\User;
use App\Services\SalesReportService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class DashboardSalesReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_empty_current_week_has_zero_totals_and_seven_days(): void
    {
        Carbon::setTestNow('2026-10-08 12:00:00');

        try {
            $report = app(SalesReportService::class)->getCurrentWeekReport();

            $this->assertSame(0, $report['total_sales']);
            $this->assertSame(0.0, $report['total_revenue']);
            $this->assertSame(0.0, $report['average_check']);
            $this->assertCount(7, $report['daily_sales']);
        } finally {
            Carbon::setTestNow();
        }
    }

    public function test_current_week_report_counts_paid_orders_and_fills_empty_days(): void
    {
        Carbon::setTestNow('2026-10-08 12:00:00');

        try {
            $user = $this->createUser();
            $this->createOrder($user, Order::STATUS_PAID, '2026-10-05 10:00:00', '100.00');
            $this->createOrder($user, Order::STATUS_SHIPPED, '2026-10-08 11:00:00', '50.50');
            $this->createOrder($user, Order::STATUS_COMPLETED, '2026-10-08 09:00:00', '24.50');
            $this->createOrder($user, Order::STATUS_PENDING, '2026-10-08 08:00:00', '1000.00');
            $this->createOrder($user, Order::STATUS_CANCELED, '2026-10-08 08:00:00', '1000.00');
            $this->createOrder($user, Order::STATUS_PAID, '2026-10-04 12:00:00', '1000.00');
            $this->createOrder($user, Order::STATUS_PAID, '2026-10-12 12:00:00', '1000.00');

            $report = app(SalesReportService::class)->getCurrentWeekReport();

            $this->assertSame(['from' => '2026-10-05', 'to' => '2026-10-11'], $report['period']);
            $this->assertSame(3, $report['total_sales']);
            $this->assertSame(175.0, $report['total_revenue']);
            $this->assertSame(58.33, $report['average_check']);
            $this->assertCount(7, $report['daily_sales']);
            $this->assertSame(['date' => '2026-10-05', 'sales_count' => 1, 'revenue' => 100.0], $report['daily_sales'][0]);
            $this->assertSame(['date' => '2026-10-06', 'sales_count' => 0, 'revenue' => 0.0], $report['daily_sales'][1]);
            $this->assertSame(['date' => '2026-10-08', 'sales_count' => 2, 'revenue' => 75.0], $report['daily_sales'][3]);
        } finally {
            Carbon::setTestNow();
        }
    }

    public function test_admin_dashboard_displays_current_week_report(): void
    {
        Carbon::setTestNow('2026-10-08 12:00:00');

        try {
            $user = $this->createUser();
            $role = Role::create(['name' => 'Администратор', 'slug' => Role::ROLE_ADMIN]);
            $user->roles()->attach($role);
            $this->createOrder($user, Order::STATUS_PAID, '2026-10-08 11:00:00', '150.50');

            $this->actingAs($user)
                ->get(route('admin.dashboard'))
                ->assertOk()
                ->assertSee('Продажи за текущую неделю')
                ->assertSee('05.10.2026')
                ->assertSee('11.10.2026')
                ->assertSee('150,50 ₽');
        } finally {
            Carbon::setTestNow();
        }
    }

    public function test_scheduler_recalculates_recent_reports_without_duplicates(): void
    {
        Carbon::setTestNow('2026-10-08 12:00:00');

        try {
            $user = $this->createUser();
            $role = Role::create(['name' => 'Администратор', 'slug' => Role::ROLE_ADMIN]);
            $user->roles()->attach($role);
            $this->createOrder($user, Order::STATUS_PAID, '2026-10-08 09:00:00', '150.50');
            $this->createOrder($user, Order::STATUS_PENDING, '2026-10-08 10:00:00', '90.00');
            $this->createOrder($user, Order::STATUS_CANCELED, '2026-10-08 11:00:00', '80.00');

            Artisan::call('schedule:run');

            $this->assertDatabaseCount('daily_sales_reports', 7);
            $this->assertDatabaseHas('daily_sales_reports', [
                'report_date' => '2026-10-08',
                'orders_count' => 3,
                'sales_count' => 1,
                'revenue' => 150.50,
                'canceled_count' => 1,
            ]);

            $this->actingAs($user)
                ->get(route('admin.dashboard'))
                ->assertOk()
                ->assertSee('Дневные отчеты за последние 7 дней')
                ->assertSee('08.10.2026');

            $this->createOrder($user, Order::STATUS_COMPLETED, '2026-10-08 11:30:00', '49.50');
            app(SalesReportService::class)->refreshRecentReports();

            $this->assertDatabaseCount('daily_sales_reports', 7);
            $dailyReport = DailySalesReport::query()->whereDate('report_date', '2026-10-08')->firstOrFail();
            $this->assertSame(4, $dailyReport->orders_count);
            $this->assertSame(2, $dailyReport->sales_count);
            $this->assertSame('200.00', $dailyReport->revenue);
        } finally {
            Carbon::setTestNow();
        }
    }

    private function createUser(): User
    {
        return User::create([
            'first_name' => 'Тест',
            'last_name' => 'Покупатель',
            'email' => 'customer@example.com',
            'password' => 'password',
        ]);
    }

    private function createOrder(User $user, string $status, string $createdAt, string $total): void
    {
        $order = Order::create([
            'user_id' => $user->id,
            'status' => $status,
            'total' => $total,
        ]);

        $order->created_at = Carbon::parse($createdAt);
        $order->save();
    }
}
