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
        Schema::create('event_types', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });

        Schema::create('event_type_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_type_id')->constrained()->onDelete('cascade');

            $table->string('locale')->index();
            $table->string('name');

            $table->unique(['event_type_id', 'locale']);
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_types');
    }
};
