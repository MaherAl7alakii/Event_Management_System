<?php

namespace Database\Seeders;

use App\Enums\BookingStatus;
use App\Enums\PricingType;
use App\Models\Booking;
use App\Models\Event;
use App\Models\Service;
use Illuminate\Database\Seeder;

class BookingSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Clear old bookings
        |--------------------------------------------------------------------------
        */

        Booking::query()->delete();

        /*
        |--------------------------------------------------------------------------
        | Helpers
        |--------------------------------------------------------------------------
        */

       $getService = function (
    string $title,
    ?string $providerEmail = null
): Service {

    $query = Service::whereHas('translations', function ($query) use ($title) {
        $query->where('locale', 'en')
            ->where('title', $title);
    });

    if ($providerEmail) {
        $query->whereHas('provider', function ($query) use ($providerEmail) {
            $query->where('email', $providerEmail);
        });
    }

    return $query->firstOrFail();
};

        $createBooking = function (
            Event $event,
            Service $service,
            string $status,
            string $startTime,
            ?int $duration,
            ?int $quantity = null,
            ?string $notes = null,
            ?float $finalPrice = null
        ) {
            $pricingType = $service->pricing_type;

            /*
            |--------------------------------------------------------------------------
            | Calculate estimated price
            |--------------------------------------------------------------------------
            */

            $basePrice = (float) $service->base_price;

            $estimatedPrice = match ($pricingType->value ?? $pricingType) {

                'fixed' =>
                    $basePrice,

                'per_hour' =>
                    $basePrice * ($duration ?? 1),

                'per_person' =>
                    $basePrice * ($quantity ?? $event->guests_count),

                'per_hour_per_person' =>
                    $basePrice
                    * ($duration ?? 1)
                    * ($quantity ?? $event->guests_count),

                default =>
                    $basePrice,
            };

            $estimatedPrice = round($estimatedPrice, 2);

            $finalPrice ??= in_array($status, [
                BookingStatus::CONFIRMED->value,
                BookingStatus::COMPLETED->value,
            ])
                ? $estimatedPrice
                : null;

            /*
            |--------------------------------------------------------------------------
            | Dates
            |--------------------------------------------------------------------------
            */

            $bookingDate = $event->event_date;

            $submittedAt = in_array($status, [
                'pending',
                'accepted',
                'deposit_paid',
                'confirmed',
                'completed',
                'cancelled',
                'rejected',
                'expired',
            ]) ? now()->subDays(rand(2, 20)) : null;

            $acceptedAt = in_array($status, [
                'accepted',
                'deposit_paid',
                'confirmed',
                'completed',
            ])
                ? now()->subDays(rand(1, 10))
                : null;

            $confirmedAt = in_array($status, [
                'confirmed',
                'completed',
            ])
                ? now()->subDays(rand(1, 5))
                : null;

            $completedAt = $status === 'completed'
                ? now()->subDays(rand(1, 3))
                : null;

            $cancelledAt = $status === 'cancelled'
                ? now()->subDays(rand(1, 5))
                : null;

            $rejectedAt = $status === 'rejected'
                ? now()->subDays(rand(1, 5))
                : null;

            /*
            |--------------------------------------------------------------------------
            | Create booking
            |--------------------------------------------------------------------------
            */

            return Booking::create([
                'service_id' => $service->id,
                'event_id' => $event->id,

                'customer_id' => $event->customer_id,
                'provider_id' => $service->provider_id,

                'pricing_type' => $pricingType->value ?? $pricingType,

                'base_price' => $basePrice,

                'estimated_price' => $estimatedPrice,

                'final_price' => $finalPrice,

                'deposit_amount_paid' => in_array($status, [
                    'deposit_paid',
                    'confirmed',
                    'completed',
                ])
                    ? round($estimatedPrice * 0.30, 2)
                    : null,

                'booking_date' => $bookingDate,

                'start_time' => $startTime,

                'duration' => $duration,

                'buffer_after_minutes' => 30,

                'quantity' => $quantity,

                'status' => $status,

                'customer_notes' => $notes,

                'submitted_at' => $submittedAt,

                'accepted_at' => $acceptedAt,

                'deposit_deadline_at' => $status === 'accepted'
                    ? now()->addDays(2)
                    : null,

                'final_payment_deadline_at' => in_array($status, [
                    'deposit_paid',
                    'confirmed',
                ])
                    ? now()->addDays(5)
                    : null,

                'confirmed_at' => $confirmedAt,

                'completed_at' => $completedAt,

                'payout_deadline_at' => $status === 'completed'
                    ? now()->addDays(7)
                    : null,

                'rejected_at' => $rejectedAt,

                'cancelled_at' => $cancelledAt,
            ]);
        };

        /*
        |--------------------------------------------------------------------------
        | Services
        |--------------------------------------------------------------------------
        */

        $weddingPhotography = $getService('Wedding Photography Package');
        $eventPhotography = $getService('Event Photography');

        $bridalMakeup = $getService('Bridal Makeup');
        $eventMakeup = $getService('Event Makeup');

        $weddingDecoration = $getService('Wedding Decoration');
        $floralDecoration = $getService('Floral Decoration Service');

        $weddingHall = $getService('Wedding Hall');
        $privateHall = $getService('Private Event Hall');

        $dj = $getService('DJ and Sound System');
        $weddingDj = $getService('Wedding DJ Package');

        $weddingCake = $getService('Custom Wedding Cake');
        $dessertTable = $getService('Dessert Table');

        $buffet = $getService('Wedding Buffet');
        $catering = $getService('Event Catering Service');

        $luxuryCar = $getService('Luxury Car Rental');
        $weddingCar = $getService('Wedding Car Service');

        $tarekPhotography = $getService('Wedding Photography');
        $tarekDj = $getService('DJ and Event Sound');

        $eventDecoration = $getService('Event Decoration');
        $outdoorVenue = $getService('Outdoor Event Venue');
       $linaBridalMakeup =
    $getService('Bridal Makeup', 'lina.hassan@gmail.com');

$linaEventMakeup =
    $getService('Event Makeup', 'lina.hassan@gmail.com');

$linaProfessionalMakeup =
    $getService('Professional Event Makeup', 'lina.hassan@gmail.com');

$linaBridalPhotography =
    $getService('Bridal Photography Package', 'lina.hassan@gmail.com');

$linaPhotography =
    $getService('Photography', 'lina.hassan@gmail.com');

$linaCake =
    $getService('Custom Celebration Cake', 'lina.hassan@gmail.com');

$linaDessertBoxes =
    $getService('Dessert Boxes', 'lina.hassan@gmail.com');
        /*
        |--------------------------------------------------------------------------
        | Mohammad
        |--------------------------------------------------------------------------
        */

        $event = Event::where('title', 'Mohammad Wedding')->first();

        if ($event) {
            $createBooking(
                $event,
                $weddingPhotography,
                'completed',
                '16:30',
                null,
                1,
                'Full wedding photography coverage.'
            );

            $createBooking(
                $event,
                $weddingDecoration,
                'confirmed',
                '14:00',
                null,
                1,
                'Wedding decoration and floral setup.'
            );

            $createBooking(
                $event,
                $weddingHall,
                'deposit_paid',
                '16:00',
                7,
                250,
                'Wedding hall reservation.'
            );
        }

        $event = Event::where('title', 'Mohammad Birthday Celebration')->first();

        if ($event) {
            $createBooking(
                $event,
                $eventPhotography,
                'accepted',
                '18:00',
                4,
                1,
                'Birthday event photography.'
            );

            $createBooking(
                $event,
                $privateHall,
                'pending',
                '18:00',
                4,
                60,
                'Private birthday celebration.'
            );
            /*
|--------------------------------------------------------------------------
| Lina Hassan - Makeup Artist
|--------------------------------------------------------------------------
*/

$event = Event::where('title', 'Lina Completed Bridal Event')->first();

if ($event) {

    $createBooking(
        $event,
        $linaBridalMakeup,
        'completed',
        '16:00',
        4,
        1,
        'Completed bridal makeup service for testing provider history.'
    );
}


$event = Event::where('title', 'Lina Tomorrow Bridal Event')->first();

if ($event) {

    $createBooking(
        $event,
        $linaBridalMakeup,
        'confirmed',
        '10:00',
        4,
        1,
        'Confirmed bridal makeup appointment for tomorrow.'
    );
}


$event = Event::where('title', 'Lina Tomorrow Event Makeup')->first();

if ($event) {

    $createBooking(
        $event,
        $linaEventMakeup,
        'pending',
        '17:00',
        null,
        5,
        'Makeup request for five guests. Waiting for provider approval.'
    );
}


$event = Event::where('title', 'Lina Future Makeup Event')->first();

if ($event) {

    $createBooking(
        $event,
        $linaProfessionalMakeup,
        'accepted',
        '15:00',
        3,
        1,
        'Professional makeup service for an engagement event.'
    );
}


$event = Event::where('title', 'Lina Photography Event')->first();

if ($event) {

    $createBooking(
        $event,
        $linaBridalPhotography,
        'deposit_paid',
        '16:00',
        null,
        1,
        'Bridal photography package with deposit already paid.'
    );
}


$event = Event::where('title', 'Lina Cake Event')->first();

if ($event) {

    $createBooking(
        $event,
        $linaCake,
        'confirmed',
        '18:00',
        null,
        1,
        'Custom celebration cake for a birthday event.'
    );
}


$event = Event::where('title', 'Lina Dessert Boxes Event')->first();

if ($event) {

    $createBooking(
        $event,
        $linaDessertBoxes,
        'pending',
        '17:00',
        null,
        40,
        'Dessert boxes for forty guests. Waiting for provider response.'
    );
}
        }

        /*
        |--------------------------------------------------------------------------
        | Sara
        |--------------------------------------------------------------------------
        */

        $event = Event::where('title', 'Sara Engagement Party')->first();

        if ($event) {
            $createBooking(
                $event,
                $bridalMakeup,
                'accepted',
                '15:00',
                3,
                1,
                'Makeup for engagement party.'
            );

            $createBooking(
                $event,
                $floralDecoration,
                'deposit_paid',
                '13:00',
                5,
                1,
                'Floral decoration for engagement.'
            );

            $createBooking(
                $event,
                $dessertTable,
                'pending',
                '16:00',
                null,
                1,
                'Dessert table for engagement party.'
            );
        }

        $event = Event::where('title', 'Sara Wedding')->first();

        if ($event) {
            $createBooking(
                $event,
                $weddingPhotography,
                'confirmed',
                '16:00',
                null,
                1
            );

            $createBooking(
                $event,
                $weddingDj,
                'deposit_paid',
                '17:00',
                6,
                1
            );

            $createBooking(
                $event,
                $buffet,
                'accepted',
                '18:00',
                4,
                300
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Rami
        |--------------------------------------------------------------------------
        */

        $event = Event::where('title', 'Rami Corporate Event')->first();

        if ($event) {
            $createBooking(
                $event,
                $eventPhotography,
                'accepted',
                '10:00',
                6,
                1,
                'Corporate event photography.'
            );

            $createBooking(
                $event,
                $catering,
                'confirmed',
                '11:00',
                5,
                120,
                'Corporate catering.'
            );

            $createBooking(
                $event,
                $dj,
                'pending',
                '12:00',
                5,
                1
            );
        }

        $event = Event::where('title', 'Rami Wedding')->first();

        if ($event) {
            $createBooking(
                $event,
                $weddingPhotography,
                'deposit_paid',
                '17:00',
                null,
                1
            );

            $createBooking(
                $event,
                $weddingCake,
                'confirmed',
                '15:00',
                null,
                1
            );

            $createBooking(
                $event,
                $weddingCar,
                'accepted',
                '16:00',
                6,
                1
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Dima
        |--------------------------------------------------------------------------
        */

        $event = Event::where('title', 'Dima Birthday Party')->first();

        if ($event) {
            $createBooking(
                $event,
                $eventPhotography,
                'confirmed',
                '17:00',
                4,
                1
            );

            $createBooking(
                $event,
                $dessertTable,
                'deposit_paid',
                '17:00',
                null,
                1
            );

            $createBooking(
                $event,
                $privateHall,
                'accepted',
                '17:00',
                5,
                80
            );
        }

        $event = Event::where('title', 'Dima Engagement')->first();

        if ($event) {
            $createBooking(
                $event,
                $bridalMakeup,
                'completed',
                '15:00',
                3,
                1
            );

            $createBooking(
                $event,
                $floralDecoration,
                'confirmed',
                '14:00',
                5,
                1
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Karim
        |--------------------------------------------------------------------------
        */

        $event = Event::where('title', 'Karim Wedding')->first();

        if ($event) {
            $createBooking(
                $event,
                $weddingPhotography,
                'deposit_paid',
                '16:00',
                null,
                1
            );

            $createBooking(
                $event,
                $weddingDj,
                'confirmed',
                '17:00',
                6,
                1
            );

            $createBooking(
                $event,
                $buffet,
                'accepted',
                '18:00',
                5,
                400
            );

            $createBooking(
                $event,
                $weddingCar,
                'pending',
                '15:30',
                6,
                1
            );
        }

        $event = Event::where('title', 'Karim Graduation Party')->first();

        if ($event) {
            $createBooking(
                $event,
                $eventPhotography,
                'completed',
                '17:00',
                5,
                1
            );

            $createBooking(
                $event,
                $privateHall,
                'completed',
                '17:00',
                5,
                100
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Lama
        |--------------------------------------------------------------------------
        */

        $event = Event::where('title', 'Lama Birthday Celebration')->first();

        if ($event) {
            $createBooking(
                $event,
                $eventPhotography,
                'confirmed',
                '18:00',
                4,
                1
            );

            $createBooking(
                $event,
                $dessertTable,
                'deposit_paid',
                '18:00',
                null,
                1
            );
        }

        $event = Event::where('title', 'Lama Wedding')->first();

        if ($event) {
            $createBooking(
                $event,
                $weddingPhotography,
                'completed',
                '16:00',
                null,
                1
            );

            $createBooking(
                $event,
                $weddingDecoration,
                'completed',
                '14:00',
                null,
                1
            );

            $createBooking(
                $event,
                $weddingDj,
                'completed',
                '17:00',
                6,
                1
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Hussein
        |--------------------------------------------------------------------------
        */

        $event = Event::where('title', 'Hussein Company Event')->first();

        if ($event) {
            $createBooking(
                $event,
                $eventPhotography,
                'completed',
                '09:00',
                6,
                1
            );

            $createBooking(
                $event,
                $catering,
                'completed',
                '11:00',
                5,
                150
            );
        }

        $event = Event::where('title', 'Hussein Wedding')->first();

        if ($event) {
            $createBooking(
                $event,
                $weddingPhotography,
                'cancelled',
                '17:00',
                null,
                1
            );

            $createBooking(
                $event,
                $weddingCar,
                'rejected',
                '16:00',
                6,
                1
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Razan
        |--------------------------------------------------------------------------
        */

        $event = Event::where('title', 'Razan Engagement')->first();

        if ($event) {
            $createBooking(
                $event,
                $bridalMakeup,
                'cancelled',
                '15:00',
                3,
                1
            );

            $createBooking(
                $event,
                $floralDecoration,
                'rejected',
                '14:00',
                5,
                1
            );
        }

        $event = Event::where('title', 'Razan Birthday')->first();

        if ($event) {
            $createBooking(
                $event,
                $eventPhotography,
                'expired',
                '17:00',
                4,
                1
            );

            $createBooking(
                $event,
                $privateHall,
                'expired',
                '17:00',
                5,
                50
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Tamer
        |--------------------------------------------------------------------------
        */

        $event = Event::where('title', 'Tamer Wedding')->first();

        if ($event) {
            $createBooking(
                $event,
                $tarekPhotography,
                'draft',
                '16:00',
                6,
                1
            );

            $createBooking(
                $event,
                $weddingDecoration,
                'draft',
                '14:00',
                null,
                1
            );
        }

        $event = Event::where('title', 'Tamer Business Event')->first();

        if ($event) {
            $createBooking(
                $event,
                $eventPhotography,
                'pending',
                '10:00',
                6,
                1
            );

            $createBooking(
                $event,
                $catering,
                'accepted',
                '11:00',
                5,
                100
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Jana
        |--------------------------------------------------------------------------
        */

        $event = Event::where('title', 'Jana Graduation Party')->first();

        if ($event) {
            $createBooking(
                $event,
                $eventPhotography,
                'accepted',
                '17:00',
                5,
                1
            );

            $createBooking(
                $event,
                $privateHall,
                'deposit_paid',
                '17:00',
                5,
                120
            );

            $createBooking(
                $event,
                $dessertTable,
                'pending',
                '17:00',
                null,
                1
            );
        }

        $event = Event::where('title', 'Jana Engagement')->first();

        if ($event) {
            $createBooking(
                $event,
                $bridalMakeup,
                'confirmed',
                '15:00',
                3,
                1
            );

            $createBooking(
                $event,
                $floralDecoration,
                'confirmed',
                '14:00',
                5,
                1
            );

            $createBooking(
                $event,
                $dessertTable,
                'deposit_paid',
                '17:00',
                null,
                1
            );
        }
    }
}