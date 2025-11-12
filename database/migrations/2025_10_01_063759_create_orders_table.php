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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->date('order_date');
            $table->string('order_number')->unique();
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->foreignId('cash_id')->constrained('cash')->onDelete('cascade');
            $table->decimal('total_cost_before', 15, 2)->default(0);
            $table->decimal('percent_discount', 5, 2)->default(0);
            $table->decimal('amount_discount', 15, 2)->default(0);
            $table->decimal('input_payment', 15, 2)->default(0);
            $table->decimal('return_payment', 15, 2)->default(0);
            $table->decimal('total_cost', 15, 2)->default(0);
            $table->string('status')->default('pending');
            $table->string('image')->nullable();
            $table->text('description')->nullable();
            $table->string('type_payment')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
