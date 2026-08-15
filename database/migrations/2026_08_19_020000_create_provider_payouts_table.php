<?php

use App\Enums\ProviderPayoutStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('provider_payouts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();

            $table->foreignId('provider_id')->constrained('users')->cascadeOnDelete();

            $table->foreignId('payment_id')->nullable()->constrained('payments')->nullOnDelete();

            $table->decimal('amount', 10, 2);

            $table->string('reason');

            $table->enum('status', array_column(ProviderPayoutStatus::cases(), 'value'));

            $table->timestamp('release_at')->nullable();

            $table->string('stripe_transfer_id')->nullable()->unique();
            $table->text('failure_reason')->nullable();
            $table->text('hold_reason')->nullable();

            $table->timestamps();

            $table->index(['status', 'release_at']);
            $table->index(['provider_id', 'status']);
            $table->index(['booking_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('provider_payouts');
    }
};
