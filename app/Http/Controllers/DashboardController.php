<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(private readonly DashboardService $dashboard)
    {
    }

    /**
     * GET /api/dashboard/overview
     */
    public function overview(): JsonResponse
    {
        return response()->json($this->dashboard->overview());
    }

    /**
     * GET /api/dashboard/bookings-chart?period=year&year=2026&status=completed
     * GET /api/dashboard/bookings-chart?period=month&year=2026&month=8
     * GET /api/dashboard/bookings-chart?period=week&week_start=2026-08-10
     */
    public function bookingsChart(Request $request): JsonResponse
    {
        return response()->json($this->dashboard->bookingsChart($request->only([
            'period', 'year', 'month', 'week_start', 'status',
        ])));
    }

    /**
     * GET /api/dashboard/payments-chart?period=year&year=2026&status=succeeded
     */
    public function paymentsChart(Request $request): JsonResponse
    {
        return response()->json($this->dashboard->paymentsChart($request->only([
            'period', 'year', 'month', 'week_start', 'status',
        ])));
    }

    /**
     * GET /api/dashboard/refunds-chart?period=year&year=2026&status=succeeded
     */
    public function refundsChart(Request $request): JsonResponse
    {
        return response()->json($this->dashboard->refundsChart($request->only([
            'period', 'year', 'month', 'week_start', 'status',
        ])));
    }

    /**
     * GET /api/dashboard/payouts-chart?period=year&year=2026&status=released
     */
    public function payoutsChart(Request $request): JsonResponse
    {
        return response()->json($this->dashboard->payoutsChart($request->only([
            'period', 'year', 'month', 'week_start', 'status',
        ])));
    }

    /**
     * GET /api/dashboard/providers-count?status=approved
     */
    public function providersCount(Request $request): JsonResponse
    {
        $status = $request->query('status');

        if ($status) {
            return response()->json([
                'status' => $status,
                'count'  => $this->dashboard->providersCountByStatus($status),
            ]);
        }

        return response()->json($this->dashboard->providersCountGroupedByStatus());
    }
}
