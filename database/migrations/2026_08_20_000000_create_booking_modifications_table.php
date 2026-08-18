<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking_modifications', function (Blueprint $table) {
            $table->id();

            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();
            $table->foreignId('requested_by')->constrained('users')->cascadeOnDelete();

            $table->json('old_values');
            $table->json('new_values');


            $table->boolean('price_changed')->default(false);
            $table->decimal('old_price', 10, 2)->nullable();
            $table->decimal('new_price', 10, 2)->nullable();


            $table->string('status')->default('pending');

            $table->boolean('requires_customer_approval')->default(false);
            $table->boolean('requires_provider_approval')->default(false);

            $table->boolean('customer_approved')->nullable();
            $table->timestamp('customer_responded_at')->nullable();

            $table->boolean('provider_approved')->nullable();
            $table->timestamp('provider_responded_at')->nullable();

            $table->timestamp('respond_by')->nullable();

            $table->timestamps();

            $table->index(['booking_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_modifications');
    }
};
