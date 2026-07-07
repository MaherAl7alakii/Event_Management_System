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
        Schema::create('events', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id');
            $table->foreignId('event_type_id')->nullable();
            $table->foreignId('city_id');

            $table->string('other_type')->nullable();
            $table->string('title');
            $table->string('cover_image')->nullable();

            $table->date('event_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('guests_count');

            $table->enum('status', [
                'draft',
                'submitted',
                'partially_accepted',
                'awaiting_payment',
                'deposit_paid',
                'confirmed',
                'completed',
                'cancelled',
                'expired'
            ])->default('draft');

            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('confirmed_at')->nullable();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
