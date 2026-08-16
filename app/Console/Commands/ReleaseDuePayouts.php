<?php

namespace App\Console\Commands;

use App\Jobs\ReleaseProviderPayoutJob;
use App\Models\ProviderPayout;
use Illuminate\Console\Command;


class ReleaseDuePayouts extends Command
{
    protected $signature = 'payouts:release-due';

    protected $description = 'Dispatch release jobs for provider payouts whose 24h grace period has elapsed.';

    public function handle(): int
    {
        $dueIds = ProviderPayout::query()
            ->dueForRelease()
            ->pluck('id');

        foreach ($dueIds as $payoutId) {
            ReleaseProviderPayoutJob::dispatch($payoutId);
        }

        $this->info("Dispatched {$dueIds->count()} payout release job(s).");

        return self::SUCCESS;
    }
}
