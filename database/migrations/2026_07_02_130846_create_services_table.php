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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('provider_id')->constrained('users')->cascadeOnUpdate();
            $table->foreignId('category_id')->constrained()->cascadeOnUpdate();
            $table->foreignId('city_id')->constrained()->cascadeOnUpdate();
            $table->enum('pricing_type', [
                'fixed',
                'per_hour',
                'per_person',
                'per_hour_per_person'
            ]);
            $table->decimal('base_price', 10, 2);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('min_hours')->nullable();
            $table->unsignedInteger('max_hours')->nullable();
            $table->unsignedInteger('max_guests')->nullable();
            $table->decimal('rating', 3, 2)->default(0);
            $table->unsignedInteger('rating_count')->default(0);
            $table->unsignedInteger('reviews_count')->default(0);
            $table->unsignedInteger('bookings_count')->default(0);

            $table->timestamps();
            $table->softDeletes();

            $table->index('category_id');
            $table->index('city_id');
            $table->index('is_active');
            $table->index('pricing_type');
        });

        Schema::create('service_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->string('locale')->index();

            $table->string('title');
            $table->text('description');
            $table->text('address')->nullable();

            $table->unique(['service_id', 'locale']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
