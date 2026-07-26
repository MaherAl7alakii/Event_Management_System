<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('working_hours', function (Blueprint $table) {
            $table->id();

            $table->foreignId('service_provider_id')
                ->constrained('service_providers')
                ->cascadeOnDelete();

            $table->unsignedTinyInteger('day_of_week');

            $table->time('start_time');
            $table->time('end_time');


            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->unique(['service_provider_id', 'day_of_week'], 'sp_working_hours_day_unique');;
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_provider_working_hours');
    }
};
