<?php

namespace App\Console\Commands;

use App\Enums\BookingModificationStatus;
use App\Models\BookingModification;
use App\Services\BookingModificationService;
use Illuminate\Console\Command;


class ExpireStaleBookingModifications extends Command
{
    protected $signature = 'bookings:expire-stale-modifications';

    protected $description = 'Expire pending booking modification requests past their response deadline.';

    public function handle(BookingModificationService $modifications): int
    {
        $stale = BookingModification::where('status', BookingModificationStatus::PENDING->value)
            ->whereNotNull('respond_by')
            ->where('respond_by', '<=', now())
            ->get();

        foreach ($stale as $modification) {
            $modifications->expire($modification);
        }

        $this->info("Expired {$stale->count()} stale booking modification(s).");

        return self::SUCCESS;
    }
}
