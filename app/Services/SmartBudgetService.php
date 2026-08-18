<?php

namespace App\Services;

use App\Models\EventTypeCategoryBudget;
use Illuminate\Support\Collection;

class SmartBudgetService
{

    public function generateDefaultPlan(int $eventTypeId, float $totalBudget): array
    {
        $defaultAllocations = EventTypeCategoryBudget::with('category')
            ->where('event_type_id', $eventTypeId)
            ->get();

        $allocations = $defaultAllocations->map(function ($item) use ($totalBudget) {
            $amount = round(($totalBudget * $item->default_percentage) / 100, 2);

            return [
                'category_id'   => $item->category_id,
                'category_name' => $item->category->name,
                'percentage'    => (float) $item->default_percentage,
                'amount'        => $amount,
            ];
        });

        return $this->buildBudgetSummary($totalBudget, $allocations);
    }


    public function recalculatePlan(float $totalBudget, array $customAllocations): array
    {
        $allocations = collect($customAllocations)->map(function ($item) use ($totalBudget) {
            $percentage = (float) ($item['percentage'] ?? 0);
            $amount = isset($item['amount'])
                ? (float) $item['amount']
                : round(($totalBudget * $percentage) / 100, 2);

            return [
                'category_id'   => $item['category_id'],
                'percentage'    => $percentage,
                'amount'        => $amount,
            ];
        });

        return $this->buildBudgetSummary($totalBudget, $allocations);
    }


    private function buildBudgetSummary(float $totalBudget, Collection $allocations): array
    {
        $totalAllocated = $allocations->sum('amount');
        $remainingBudget = round($totalBudget - $totalAllocated, 2);

        return [
            'total_budget'     => $totalBudget,
            'total_allocated'  => $totalAllocated,
            'remaining_budget' => max(0, $remainingBudget),
            'over_budget'      => round(max(0, $totalAllocated - $totalBudget), 2),
            'status'           => $remainingBudget < 0 ? 'over_budget' : ($remainingBudget > 0 ? 'remaining' : 'exact'),
            'allocations'      => $allocations->values()->toArray(),
        ];
    }
}
