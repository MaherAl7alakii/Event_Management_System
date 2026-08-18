<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\ProviderPayout;
use App\Models\Refund;
use App\Models\Service;
use App\Models\ServiceProvider;
use App\Models\User;
use Carbon\Carbon;


class DashboardService
{

    public function overview(): array
    {
        return [
            'customers_count'         => User::role('customer')->count(),
            'service_providers_count' => User::role('service_provider')->count(),
            'services_count'          => Service::count(),
            'bookings_count'          => Booking::count(),
            'payments_count'          => Payment::count(),
            'refunds_count'           => Refund::count(),
            'payouts_count'           => ProviderPayout::count(),
        ];
    }




    public function bookingsChart(array $filters = []): array
    {
        return $this->buildChart(Booking::class, 'created_at', 'status', $filters);
    }


    public function paymentsChart(array $filters = []): array
    {
        return $this->buildChart(Payment::class, 'created_at', 'status', $filters);
    }


    public function refundsChart(array $filters = []): array
    {
        return $this->buildChart(Refund::class, 'created_at', 'status', $filters);
    }


    public function payoutsChart(array $filters = []): array
    {
        return $this->buildChart(ProviderPayout::class, 'created_at', 'status', $filters);
    }


    public function providersCountByStatus(string $status): int
    {
        return ServiceProvider::where('approval_status', $status)->count();
    }

    public function providersCountGroupedByStatus(): array
    {
        $statuses = ['pending', 'approved', 'rejected'];

        $result = [];
        foreach ($statuses as $status) {
            $result[$status] = $this->providersCountByStatus($status);
        }

        return $result;
    }


    private function buildChart(string $modelClass, string $dateColumn, string $statusColumn, array $filters): array
    {
        $period = $filters['period'] ?? 'year';
        $year   = (int) ($filters['year'] ?? now()->year);
        $status = $filters['status'] ?? null;

        $data = match ($period) {
            'month' => $this->countGroupedByDayOfMonth(
                $modelClass,
                $dateColumn,
                $year,
                (int) ($filters['month'] ?? now()->month),
                $statusColumn,
                $status,
            ),
            'week' => $this->countGroupedByWeek(
                $modelClass,
                $dateColumn,
                isset($filters['week_start'])
                    ? Carbon::parse($filters['week_start'])->startOfDay()
                    : now()->startOfWeek(),
                $statusColumn,
                $status,
            ),
            default => $this->countGroupedByMonth($modelClass, $dateColumn, $year, $statusColumn, $status),
        };

        return [
            'period'      => $period,
            'year'        => $year,
            'month'       => $period === 'month' ? (int) ($filters['month'] ?? now()->month) : null,
            'week_start'  => $period === 'week'
                ? (isset($filters['week_start']) ? Carbon::parse($filters['week_start'])->format('Y-m-d') : now()->startOfWeek()->format('Y-m-d'))
                : null,
            'status'      => $status,
            'total'       => array_sum(array_column($data, 'count')),
            'data'        => $data,
        ];
    }


    private function countGroupedByMonth(
        string $modelClass,
        string $dateColumn,
        int $year,
        ?string $statusColumn = null,
        ?string $status = null,
    ): array {
        $query = $modelClass::query()->whereYear($dateColumn, $year);

        if ($statusColumn && $status) {
            $query->where($statusColumn, $status);
        }

        $raw = $query
            ->selectRaw("MONTH({$dateColumn}) as period, COUNT(*) as total")
            ->groupBy('period')
            ->pluck('total', 'period');

        $result = [];
        for ($m = 1; $m <= 12; $m++) {
            $result[] = [
                'month' => $m,
                'label' => Carbon::create($year, $m, 1)->translatedFormat('F'),
                'count' => (int) ($raw[$m] ?? 0),
            ];
        }

        return $result;
    }


    private function countGroupedByDayOfMonth(
        string $modelClass,
        string $dateColumn,
        int $year,
        int $month,
        ?string $statusColumn = null,
        ?string $status = null,
    ): array {
        $query = $modelClass::query()
            ->whereYear($dateColumn, $year)
            ->whereMonth($dateColumn, $month);

        if ($statusColumn && $status) {
            $query->where($statusColumn, $status);
        }

        $raw = $query
            ->selectRaw("DAY({$dateColumn}) as period, COUNT(*) as total")
            ->groupBy('period')
            ->pluck('total', 'period');

        $daysInMonth = Carbon::create($year, $month, 1)->daysInMonth;

        $result = [];
        for ($d = 1; $d <= $daysInMonth; $d++) {
            $result[] = [
                'day'   => $d,
                'label' => Carbon::create($year, $month, $d)->format('Y-m-d'),
                'count' => (int) ($raw[$d] ?? 0),
            ];
        }

        return $result;
    }


    private function countGroupedByWeek(
        string $modelClass,
        string $dateColumn,
        Carbon $weekStart,
        ?string $statusColumn = null,
        ?string $status = null,
    ): array {
        $weekEnd = $weekStart->copy()->addDays(6);

        $query = $modelClass::query()
            ->whereDate($dateColumn, '>=', $weekStart->format('Y-m-d'))
            ->whereDate($dateColumn, '<=', $weekEnd->format('Y-m-d'));

        if ($statusColumn && $status) {
            $query->where($statusColumn, $status);
        }

        $raw = $query
            ->selectRaw("DATE({$dateColumn}) as period, COUNT(*) as total")
            ->groupBy('period')
            ->pluck('total', 'period');

        $result = [];
        for ($i = 0; $i < 7; $i++) {
            $date = $weekStart->copy()->addDays($i);
            $key  = $date->format('Y-m-d');

            $result[] = [
                'date'  => $key,
                'label' => $date->translatedFormat('l'),
                'count' => (int) ($raw[$key] ?? 0),
            ];
        }

        return $result;
    }
}
