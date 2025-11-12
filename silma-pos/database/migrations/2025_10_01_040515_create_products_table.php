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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('product_code')->unique();
            $table->string('barcode')->nullable()->unique();
            $table->string('name');
            $table->string('slug')->unique();
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->boolean('status_active')->default(true);
            $table->boolean('status_discount')->default(false);
            $table->boolean('status_display')->default(true);
            $table->text('note')->nullable();
            $table->integer('position')->default(0);
            $table->integer('reminder')->nullable();
            $table->string('link')->nullable();
            $table->date('expire_date')->nullable();
            $table->integer('sold')->default(0);
            $table->unsignedBigInteger('base_unit_id')->nullable();
            $table->integer('base_stock')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
