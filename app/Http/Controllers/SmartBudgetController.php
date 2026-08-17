<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\SmartBudgetService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SmartBudgetController extends Controller
{
    public function __construct(private readonly SmartBudgetService $budgetService)
    {
    }


    public function generate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'event_type_id' => ['required', 'exists:event_types,id'],
            'total_budget'  => ['required', 'numeric', 'min:1'],
        ]);

        $plan = $this->budgetService->generateDefaultPlan(
            $validated['event_type_id'],
            (float) $validated['total_budget']
        );

        return response()->json([
            'status' => 200,
            'data'   => $plan,
        ]);
    }


    public function recalculate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'total_budget'               => ['required', 'numeric', 'min:1'],
            'allocations'                => ['required', 'array'],
            'allocations.*.category_id'  => ['required', 'exists:categories,id'],
            'allocations.*.percentage'   => ['nullable', 'numeric', 'min:0', 'max:100'],
            'allocations.*.amount'       => ['nullable', 'numeric', 'min:0'],
        ]);

        $plan = $this->budgetService->recalculatePlan(
            (float) $validated['total_budget'],
            $validated['allocations']
        );

        return response()->json([
            'status' => 200,
            'data'   => $plan,
        ]);
    }
}
