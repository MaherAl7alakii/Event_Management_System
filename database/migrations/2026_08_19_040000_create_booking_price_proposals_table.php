<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking_price_proposals', function (Blueprint $table) {
            $table->id();

            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();
            $table->foreignId('proposed_by')->constrained('users')->cascadeOnDelete();

            $table->decimal('old_price', 10, 2);
            $table->decimal('new_price', 10, 2);
            $table->string('status')->default('pending');

            $table->timestamp('respond_by');

            $table->timestamp('responded_at')->nullable();

            $table->timestamps();

            $table->index(['booking_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_price_proposals');
    }
};
