<?php

namespace App\Services;

use App\Enums\ProviderPayoutStatus;
use App\Models\Payment;
use App\Models\ProviderPayout;
use App\Models\Refund;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;


class PaymentQueryService
{

    public function forCustomer(User $customer, array $filters = [], int $perPage = 1500): LengthAwarePaginator
    {
        return Payment::filter($filters)
            ->whereHas('event', fn ($q) => $q->where('customer_id', $customer->id))
            ->with(['event', 'booking.service'])
            ->latest()
            ->paginate($perPage);
    }


    public function forProvider(User $provider, array $filters = [], int $perPage = 1500): LengthAwarePaginator
    {
        return ProviderPayout::filter($filters)
            ->where('provider_id', $provider->id)
            ->with(['booking.service', 'payment'])
            ->latest()
            ->paginate($perPage);
    }



    public function allPayments(array $filters = [], int $perPage = 1500): LengthAwarePaginator
    {
        return Payment::filter($filters)
            ->with(['event.customer', 'booking.service'])
            ->latest()
            ->paginate($perPage);
    }


    public function allPayouts(array $filters = [], int $perPage = 1500): LengthAwarePaginator
    {
        return ProviderPayout::filter($filters)
            ->with(['provider', 'booking.service', 'payment'])
            ->latest()
            ->paginate($perPage);
    }


    public function providerEarningsSummary(User $provider): array
    {
        $payouts = ProviderPayout::where('provider_id', $provider->id)->get();

        return [
            'total_released'   => (float) $payouts->where('status', ProviderPayoutStatus::RELEASED->value)->sum('amount'),
            'pending_release'  => (float) $payouts->whereIn('status', [
                ProviderPayoutStatus::SCHEDULED_FOR_COMPLETION->value,
                ProviderPayoutStatus::AWAITING_RELEASE->value,
            ])->sum('amount'),
            'on_hold'          => (float) $payouts->where('status', ProviderPayoutStatus::ON_HOLD->value)->sum('amount'),
        ];
    }



    public function refundsForCustomer(User $customer, array $filters = [], int $perPage = 1500): LengthAwarePaginator
    {
        return Refund::filter($filters)
            ->whereHas('payment.event', fn ($q) => $q->where('customer_id', $customer->id))
            ->with(['payment.event', 'payment.booking.service'])
            ->latest()
            ->paginate($perPage);
    }




    public function allRefunds(array $filters = [], int $perPage = 1500): LengthAwarePaginator
    {
        return Refund::filter($filters)
            ->with(['payment.event.customer', 'payment.booking.service'])
            ->latest()
            ->paginate($perPage);
    }
}
