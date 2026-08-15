<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('refunds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')->constrained('payments')->cascadeOnDelete();
            $table->decimal('amount', 10, 2);

            $table->string('reason');

            $table->string('stripe_refund_id')->nullable()->unique();

            $table->string('status')->default('pending');
            $table->text('failure_reason')->nullable();

            $table->foreignId('initiated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            $table->index(['payment_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('refunds');
    }
};
