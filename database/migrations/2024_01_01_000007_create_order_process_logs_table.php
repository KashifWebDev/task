<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_process_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->string('step');
            $table->enum('status', ['success', 'failed'])->default('success');
            $table->integer('attempt')->default(1);
            $table->text('error_message')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        Schema::table('order_process_logs', function (Blueprint $table) {
            $table->index(['order_id', 'step', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_process_logs');
    }
};
