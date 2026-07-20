<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('event_id')->constrained('events');

            $table->foreignId('booking_id')->nullable()->constrained('bookings')->nullOnDelete();

            $table->decimal('amount', 10, 2);
            $table->string('stripe_charge_id')->nullable();
            $table->string('stripe_payment_intent_id')->nullable();

            $table->string('stripe_transfer_id')->nullable();


            $table->string('payment_type');
            $table->string('status')->default('pending');

            $table->timestamps();


            $table->index(['event_id', 'payment_type']);
            $table->index(['booking_id']);
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->unique('stripe_charge_id');
            $table->unique('stripe_payment_intent_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
