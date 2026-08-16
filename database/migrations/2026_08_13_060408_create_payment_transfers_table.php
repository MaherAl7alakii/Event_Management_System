<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')
                ->constrained('payments')
                ->cascadeOnDelete();

            $table->foreignId('booking_id')
                ->constrained('bookings')
                ->cascadeOnDelete();


            $table->foreignId('provider_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('stripe_transfer_id')->nullable()->unique();
            $table->decimal('amount', 10, 2);

            $table->string('status')->default('pending');

            $table->text('failure_reason')->nullable();

            $table->timestamps();

            $table->index(['provider_id', 'status']);
            $table->index(['payment_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_transfers');
    }
};
