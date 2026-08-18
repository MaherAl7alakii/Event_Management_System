<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Event;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Database\Factories\BookingFactory;
use Database\Factories\EventFactory;
use Database\Factories\PaymentFactory;
use Database\Factories\ProfileFactory;
use Database\Factories\ProviderPayoutFactory;
use Database\Factories\RefundFactory;
use Database\Factories\ServiceFactory;
use Database\Factories\ServiceProviderFactory;
use Database\Factories\UserFactory;
use Database\Factories\WorkingHourFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Spatie\Permission\Models\Role;


class DemoDataSeeder extends Seeder
{
    private const START_DATE = '2023-08-18 00:00:00';
    private const END_DATE   = '2026-08-18 23:59:59';

    public function run(): void
    {
        set_time_limit(0);
        Model::unguard();

        $this->command?->info('إنشاء الأدوار (roles)...');
        $this->ensureRoles();

        $this->command?->info('إنشاء 300 زبون + profiles...');
        $customers = $this->seedCustomers(200);

        $this->command?->info('إنشاء 150 مزود خدمة + working hours...');
        $providers = $this->seedProviders(100);

        $this->command?->info('إنشاء الخدمات (10 لكل مزود)...');
        $servicesByProvider = $this->seedServices($providers);

        $this->command?->info('إنشاء الأحداث والحجوزات والدفعات...');
        $this->seedEventsAndBookings($customers, $providers, $servicesByProvider);

        Model::reguard();

        $this->command?->info('انتهى بنجاح.');
    }



    private function randomDateTime(?Carbon $start = null, ?Carbon $end = null): Carbon
    {
        $start ??= Carbon::parse(self::START_DATE);
        $end   ??= Carbon::parse(self::END_DATE);

        return Carbon::createFromTimestamp(
            fake()->numberBetween($start->timestamp, $end->timestamp)
        );
    }

    private function ensureRoles(): void
    {
        foreach (['customer', 'service_provider'] as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'api']);
        }
    }

    /* -----------------------------------------------------------------
     |  Customers
     | -----------------------------------------------------------------
     */

    private function seedCustomers(int $count): Collection
    {
        $customers = collect();

        for ($i = 0; $i < $count; $i++) {
            $createdAt = $this->randomDateTime();

            $user = UserFactory::new()->create([
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);

            $user->assignRole('customer');

            ProfileFactory::new()->create([
                'user_id'    => $user->id,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);

            $customers->push($user);
        }

        return $customers;
    }

    /* -----------------------------------------------------------------
     |  Providers
     | -----------------------------------------------------------------
     */

    private function seedProviders(int $count): Collection
    {
        $statuses = array_merge(
            array_fill(0, 3, 'pending'),
            array_fill(0, 10, 'rejected'),
            array_fill(0, $count - 13, 'approved'),
        );
        shuffle($statuses);

        $providers = collect();

        foreach ($statuses as $status) {
            $createdAt = $this->randomDateTime();

            $user = UserFactory::new()->create([
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);

            $user->assignRole('service_provider');

            $factory = ServiceProviderFactory::new();
            $factory = match ($status) {
                'pending'  => $factory->pending(),
                'rejected' => $factory->rejected(),
                default    => $factory,
            };

            $serviceProvider = $factory->create([
                'user_id'    => $user->id,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);


            for ($day = 0; $day <= 6; $day++) {
                WorkingHourFactory::new()->create([
                    'service_provider_id' => $serviceProvider->id,
                    'day_of_week'          => $day,
                    'created_at'           => $createdAt,
                    'updated_at'           => $createdAt,
                ]);
            }

            $providers->push([
                'user'             => $user,
                'service_provider' => $serviceProvider,
                'approval_status'  => $status,
            ]);
        }

        return $providers;
    }


    private function seedServices(Collection $providers): Collection
    {
        $servicesByProvider = collect();

        foreach ($providers as $providerData) {
            $user = $providerData['user'];
            $sp   = $providerData['service_provider'];

            $services = collect();

            for ($i = 0; $i < 10; $i++) {
                $createdAt = $this->randomDateTime();

                $service = ServiceFactory::new()->create([
                    'provider_id' => $user->id,
                    'category_id' => fake()->numberBetween(1, 8),
                    'city_id'     => $sp->city_id,
                    'created_at'  => $createdAt,
                    'updated_at'  => $createdAt,
                ]);

                $services->push($service);
            }

            $servicesByProvider->put($user->id, $services);
        }

        return $servicesByProvider;
    }


    private function seedEventsAndBookings(Collection $customers, Collection $providers, Collection $servicesByProvider): void
    {

        $allServices = collect();

        foreach ($providers as $providerData) {
            if ($providerData['approval_status'] !== 'approved') {
                continue;
            }

            $user = $providerData['user'];

            foreach ($servicesByProvider->get($user->id) as $service) {
                $allServices->push([
                    'service'  => $service,
                    'provider' => $user,
                ]);
            }
        }

        foreach ($customers as $customer) {
            for ($e = 0; $e < 5; $e++) {
                $eventDate = $this->randomDateTime();

                $event = EventFactory::new()->create([
                    'customer_id'  => $customer->id,
                    'event_date'   => $eventDate->format('Y-m-d'),
                    'submitted_at' => $eventDate,
                    'confirmed_at' => $eventDate->copy()->addHour(),
                    'created_at'   => $eventDate,
                    'updated_at'   => $eventDate,
                ]);


                $bookingStatuses = array_merge(
                    ['cancelled'],
                    ['expired'],
                    array_fill(0, 8, 'completed'),
                );
                shuffle($bookingStatuses);

                foreach ($bookingStatuses as $status) {
                    $picked = $allServices->random();

                    $this->createBooking(
                        $event,
                        $customer,
                        $picked['service'],
                        $picked['provider'],
                        $status,
                        $eventDate,
                    );
                }
            }
        }
    }

    private function createBooking(
        Event $event,
        User $customer,
        Service $service,
        User $provider,
        string $status,
        Carbon $eventDate,
    ): void {
        $bookingCreatedAt = $eventDate->copy()->subDays(fake()->numberBetween(1, 20));
        $lowerBound       = Carbon::parse(self::START_DATE);

        if ($bookingCreatedAt->lt($lowerBound)) {
            $bookingCreatedAt = $lowerBound;
        }

        $duration         = fake()->numberBetween(60, 480);
        $quantity         = fake()->numberBetween(1, 5);
        $durationInHours  = $duration / 60;
        $basePrice        = (float) $service->base_price;

        $estimatedPrice = match ($service->pricing_type->value) {
            'fixed'                => $basePrice,
            'per_hour'              => $basePrice * $durationInHours,
            'per_person'            => $basePrice * $quantity,
            'per_hour_per_person'   => $basePrice * $durationInHours * $quantity,
            default                 => $basePrice,
        };

        $estimatedPrice = round($estimatedPrice, 2);
        $finalPrice     = $estimatedPrice;
        $depositAmount  = round($finalPrice * Booking::DEPOSIT_PERCENTAGE, 2);

        $common = [
            'service_id'           => $service->id,
            'event_id'             => $event->id,
            'customer_id'          => $customer->id,
            'provider_id'          => $provider->id,
            'pricing_type'         => $service->pricing_type->value,
            'base_price'           => $basePrice,
            'estimated_price'      => $estimatedPrice,
            'booking_date'         => $eventDate->format('Y-m-d'),
            'start_time'           => sprintf('%02d:%02d:00', fake()->numberBetween(9, 18), fake()->randomElement([0, 30])),
            'duration'             => $duration,
            'buffer_after_minutes' => fake()->numberBetween(0, 60),
            'quantity'             => $quantity,
            'submitted_at'         => $bookingCreatedAt,
            'created_at'           => $bookingCreatedAt,
            'updated_at'           => $bookingCreatedAt,
        ];

        switch ($status) {
            case 'expired':
                BookingFactory::new()->create(array_merge($common, [
                    'status'               => 'expired',
                    'final_price'          => null,
                    'deposit_amount_paid'  => null,
                    'accepted_at'          => null,
                    'confirmed_at'         => null,
                    'completed_at'         => null,
                    'cancelled_at'         => null,
                    'deposit_deadline_at'  => $bookingCreatedAt->copy()->addDays(2),
                ]));

                break;

            case 'cancelled':
                $acceptedAt  = $bookingCreatedAt->copy()->addHours(fake()->numberBetween(1, 24));
                $confirmedAt = $acceptedAt->copy()->addHours(fake()->numberBetween(1, 12));
                $cancelledAt = $confirmedAt->copy()->addDays(fake()->numberBetween(1, 5));

                if ($cancelledAt->gt($eventDate)) {
                    $cancelledAt = $eventDate->copy()->subHours(fake()->numberBetween(1, 12));
                }

                $booking = BookingFactory::new()->create(array_merge($common, [
                    'status'              => 'cancelled',
                    'final_price'         => $finalPrice,
                    'deposit_amount_paid' => $depositAmount,
                    'accepted_at'         => $acceptedAt,
                    'confirmed_at'        => $confirmedAt,
                    'completed_at'        => null,
                    'cancelled_at'        => $cancelledAt,
                    'updated_at'          => $cancelledAt,
                ]));

                $this->createCancelledLedger($booking, $depositAmount, $finalPrice, $confirmedAt, $cancelledAt);
                $service->increment('bookings_count');
                break;

            case 'completed':
            default:
                $acceptedAt  = $bookingCreatedAt->copy()->addHours(fake()->numberBetween(1, 24));
                $confirmedAt = $acceptedAt->copy()->addHours(fake()->numberBetween(1, 12));
                $completedAt = $eventDate->copy()->addHours(fake()->numberBetween(1, 6));

                $upperBound = Carbon::parse(self::END_DATE);
                if ($completedAt->gt($upperBound)) {
                    $completedAt = $upperBound;
                }

                $booking = BookingFactory::new()->create(array_merge($common, [
                    'status'              => 'completed',
                    'final_price'         => $finalPrice,
                    'deposit_amount_paid' => $depositAmount,
                    'accepted_at'         => $acceptedAt,
                    'confirmed_at'        => $confirmedAt,
                    'completed_at'        => $completedAt,
                    'cancelled_at'        => null,
                    'updated_at'          => $completedAt,
                ]));

                $this->createCompletedLedger($booking, $depositAmount, $finalPrice, $confirmedAt, $completedAt);
                $service->increment('bookings_count');
                break;
        }
    }


    private function createCompletedLedger(
        Booking $booking,
        float $depositAmount,
        float $finalPrice,
        Carbon $confirmedAt,
        Carbon $completedAt,
    ): void {
        $finalBalance = round($finalPrice - $depositAmount, 2);

        $depositPayment = PaymentFactory::new()->create([
            'event_id'     => $booking->event_id,
            'booking_id'   => $booking->id,
            'amount'       => $depositAmount,
            'payment_type' => 'deposit',
            'status'       => 'succeeded',
            'created_at'   => $confirmedAt,
            'updated_at'   => $confirmedAt,
        ]);

        $finalPayment = PaymentFactory::new()->create([
            'event_id'     => $booking->event_id,
            'booking_id'   => $booking->id,
            'amount'       => $finalBalance,
            'payment_type' => 'final_balance',
            'status'       => 'succeeded',
            'created_at'   => $completedAt,
            'updated_at'   => $completedAt,
        ]);

        $depositReleaseAt = $confirmedAt->copy()->addDay();
        ProviderPayoutFactory::new()->create([
            'booking_id'  => $booking->id,
            'provider_id' => $booking->provider_id,
            'payment_id'  => $depositPayment->id,
            'amount'      => $depositAmount,
            'reason'      => 'deposit',
            'status'      => 'released',
            'release_at'  => $depositReleaseAt,
            'created_at'  => $depositReleaseAt,
            'updated_at'  => $depositReleaseAt,
        ]);

        $finalReleaseAt = $completedAt->copy()->addDays(2);
        ProviderPayoutFactory::new()->create([
            'booking_id'  => $booking->id,
            'provider_id' => $booking->provider_id,
            'payment_id'  => $finalPayment->id,
            'amount'      => $finalBalance,
            'reason'      => 'final_balance',
            'status'      => 'released',
            'release_at'  => $finalReleaseAt,
            'created_at'  => $finalReleaseAt,
            'updated_at'  => $finalReleaseAt,
        ]);
    }

    private function createCancelledLedger(
        Booking $booking,
        float $depositAmount,
        float $finalPrice,
        Carbon $confirmedAt,
        Carbon $cancelledAt,
    ): void {
        $finalBalance = round($finalPrice - $depositAmount, 2);

        $depositPayment = PaymentFactory::new()->create([
            'event_id'     => $booking->event_id,
            'booking_id'   => $booking->id,
            'amount'       => $depositAmount,
            'payment_type' => 'deposit',
            'status'       => 'succeeded',
            'created_at'   => $confirmedAt,
            'updated_at'   => $confirmedAt,
        ]);

        $finalPaidAt = $confirmedAt->copy()->addHours(2);
        $finalPayment = PaymentFactory::new()->create([
            'event_id'     => $booking->event_id,
            'booking_id'   => $booking->id,
            'amount'       => $finalBalance,
            'payment_type' => 'final_balance',
            'status'       => 'succeeded',
            'created_at'   => $finalPaidAt,
            'updated_at'   => $finalPaidAt,
        ]);


        ProviderPayoutFactory::new()->create([
            'booking_id'  => $booking->id,
            'provider_id' => $booking->provider_id,
            'payment_id'  => $depositPayment->id,
            'amount'      => $depositAmount,
            'reason'      => 'cancellation_deposit_share',
            'status'      => 'cancelled',
            'release_at'  => null,
            'created_at'  => $cancelledAt,
            'updated_at'  => $cancelledAt,
        ]);


        ProviderPayoutFactory::new()->create([
            'booking_id'  => $booking->id,
            'provider_id' => $booking->provider_id,
            'payment_id'  => $finalPayment->id,
            'amount'      => $finalBalance,
            'reason'      => 'cancellation_full_amount',
            'status'      => 'cancelled',
            'release_at'  => null,
            'created_at'  => $cancelledAt,
            'updated_at'  => $cancelledAt,
        ]);

        $refundAt = $cancelledAt->copy()->addHours(3);
        RefundFactory::new()->create([
            'payment_id'   => $finalPayment->id,
            'amount'       => $finalBalance,
            'reason'       => 'booking_cancelled',
            'status'       => 'succeeded',
            'initiated_by' => null,
            'created_at'   => $refundAt,
            'updated_at'   => $refundAt,
        ]);


        $extraReleaseAt = $cancelledAt->copy()->addDays(3);
        ProviderPayoutFactory::new()->create([
            'booking_id'  => $booking->id,
            'provider_id' => $booking->provider_id,
            'payment_id'  => $depositPayment->id,
            'amount'      => round($depositAmount * 0.5, 2),
            'reason'      => 'cancellation_deposit_share',
            'status'      => 'released',
            'release_at'  => $extraReleaseAt,
            'created_at'  => $extraReleaseAt,
            'updated_at'  => $extraReleaseAt,
        ]);
    }
}
