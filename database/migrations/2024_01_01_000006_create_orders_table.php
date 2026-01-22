<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->enum('state', ['pending', 'confirmed', 'partially_failed', 'completed', 'cancelled'])
                  ->default('pending');
            $table->decimal('total_amount', 10, 2);
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->index('state');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
