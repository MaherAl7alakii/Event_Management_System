<?php

use App\Enums\TimeOffReason;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('time_offs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('service_provider_id')
                ->constrained('service_providers')
                ->cascadeOnDelete();


            $table->string('type');

            $table->date('start_date');
            $table->date('end_date');


            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();

            $table->enum('reason', array_column(TimeOffReason::cases(), 'value'));
            $table->text('note')->nullable();

            $table->timestamps();

            $table->index(['service_provider_id', 'start_date', 'end_date'], 'sp_time_offs_dates_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_provider_time_offs');
    }
};
