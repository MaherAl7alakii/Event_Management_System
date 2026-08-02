<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_offers', function (Blueprint $table) {

            $table->id();

            $table->foreignId('service_id')->unique()->constrained()->cascadeOnDelete();

            $table->unsignedTinyInteger('discount');

            $table->decimal('original_price',10,2);

            $table->decimal('offer_price',10,2);

            $table->date('start_date');

            $table->date('end_date');

            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_offers');
    }
};