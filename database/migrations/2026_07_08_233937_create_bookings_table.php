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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained();
            $table->foreignId('event_id')->constrained();
            $table->foreignId('customer_id')->constrained('users');
            $table->foreignId('provider_id')->constrained('users');

            $table->enum('pricing_type', [
                'fixed',
                'per_hour',
                'per_person',
                'per_hour_per_person'
            ]);
            $table->decimal('base_price', 10, 2);
            $table->decimal('estimated_price', 10, 2)->nullable();
            $table->decimal('final_price', 10, 2)->nullable();
            $table->date('booking_date');
            $table->time('start_time');
            $table->unsignedInteger('duration')->nullable();
            $table->unsignedInteger('buffer_after_minutes')->nullable();
            $table->unsignedInteger('quantity')->nullable();

            $table->enum('status', [
                'draft', 'pending', 'accepted', 'deposit_paid',
                'confirmed', 'completed', 'cancelled', 'rejected', 'expired'
            ])->default('draft');

            $table->text('customer_notes')->nullable();


            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('deposit_deadline_at')->nullable();
            $table->timestamp('final_payment_deadline_at')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('rejected_at')->nullable();




            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
