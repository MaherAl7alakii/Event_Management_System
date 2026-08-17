<?php

namespace App\Services;

use App\Exceptions\ServiceUnavailableException;
use App\Models\Event;
use App\Models\Package;
use App\Models\Service;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PackageBookingService
{
    public function __construct(
        private readonly ServiceAvailabilityService $availability,
        private readonly BookingService $bookingService,
    ) {
    }


    public function addPackageToEvent(Event $event, Package $package, array $serviceBookings, int $customerId): Collection
    {
        $package->loadMissing('services');

        $this->assertServicesMatchPackage($package, $serviceBookings);


        foreach ($serviceBookings as $entry) {
            $service = $package->services->firstWhere('id', $entry['service_id']);

            $this->assertAvailable($service, $entry);
        }

        return DB::transaction(function () use ($event, $package, $serviceBookings, $customerId) {
            $priceShares = $this->distributeProportionally($package);

            return collect($serviceBookings)->map(function (array $entry) use ($event, $package, $priceShares, $customerId) {
                $service = $package->services->firstWhere('id', $entry['service_id']);
                $estimatedPrice = $priceShares[$service->id];

                return $this->bookingService->createBookingFromData([
                    'event_id'       => $event->id,
                    'service_id'     => $service->id,
                    'package_id'     => $package->id,
                    'booking_date'   => $entry['booking_date'],
                    'start_time'     => $entry['start_time'],
                    'duration'       => $entry['duration'] ?? null,
                    'quantity'       => $entry['quantity'] ?? null,
                    'customer_notes' => $entry['customer_notes'] ?? null,

                    'estimated_price_override' => $estimatedPrice,
                ], $customerId);
            });
        });
    }


    private function distributeProportionally(Package $package): array
    {
        $totalBasePrice = (float) $package->total_price;
        $finalPrice = (float) $package->final_price;

        if ($totalBasePrice <= 0) {
            throw new Exception("Package #{$package->id} has an invalid total price.");
        }

        $shares = [];
        $runningTotal = 0.0;
        $services = $package->services;
        $lastServiceId = $services->last()->id;

        foreach ($services as $service) {
            if ($service->id === $lastServiceId) {

                $shares[$service->id] = round($finalPrice - $runningTotal, 2);
                continue;
            }

            $ratio = (float) $service->base_price / $totalBasePrice;
            $share = round($finalPrice * $ratio, 2);

            $shares[$service->id] = $share;
            $runningTotal += $share;
        }

        return $shares;
    }

    /**
     * @throws Exception
     */
//    private function assertServicesMatchPackage(Package $package, array $serviceBookings): void
//    {
//        $requestedIds = collect($serviceBookings)->pluck('service_id')->sort()->values();
//        $packageServiceIds = $package->services->pluck('id')->sort()->values();
//
//        if (! $requestedIds->equals($packageServiceIds)) {
//            throw new Exception(
//                "The provided services do not exactly match package #{$package->id}'s services. "
//                . 'A package must be booked as a whole with all its services.'
//            );
//        }
//    }

    private function assertServicesMatchPackage(Package $package, array $serviceBookings): void
    {
        $requestedIds = collect($serviceBookings)->pluck('service_id')->sort()->values()->toArray();
        $packageServiceIds = $package->services->pluck('id')->sort()->values()->toArray();

        if ($requestedIds !== $packageServiceIds) {
            throw new Exception(
                "The provided services do not exactly match package #{$package->id}'s services. "
                . 'A package must be booked as a whole with all its services.'
            );
        }
    }

    /**
     * @throws ServiceUnavailableException
     */
    private function assertAvailable(Service $service, array $entry): void
    {
        $reason = $this->availability->unavailabilityReason(
            service: $service,
            bookingDate: $entry['booking_date'],
            startTime: $entry['start_time'],
            duration: $entry['duration'] ?? null,
        );

        if ($reason !== null) {
            throw ServiceUnavailableException::forReason($service, $reason);
        }
    }
}
