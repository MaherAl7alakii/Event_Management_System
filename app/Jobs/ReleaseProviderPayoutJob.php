<?php

namespace App\Jobs;

use App\Models\ProviderPayout;
use App\Services\ProviderPayoutService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ReleaseProviderPayoutJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public readonly int $payoutId,
    ) {
    }

    public function handle(ProviderPayoutService $payoutService): void
    {
        $payout = ProviderPayout::find($this->payoutId);

        if (! $payout) {
            return;
        }

        $payoutService->releasePayout($payout);
    }
}
