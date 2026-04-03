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
        // Size options table
        Schema::create('sizes', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // S, M, L, XL, XXL, etc.
            $table->string('code'); // s, m, l, xl, xxl
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Color options table
        Schema::create('colors', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // White, Black, Blue, etc.
            $table->string('code'); // white, black, blue
            $table->string('hex_code')->nullable(); // #FFFFFF
            $table->timestamps();
        });

        // Product variants (size + color combinations with stock)
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('size_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('color_id')->nullable()->constrained()->onDelete('set null');
            $table->string('sku')->unique()->nullable(); // Variant-specific SKU
            $table->decimal('price', 10, 2)->nullable(); // Variant-specific price override
            $table->integer('stock_in')->default(0);
            $table->integer('stock_out')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['product_id', 'size_id', 'color_id']);
            $table->index('product_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variants');
        Schema::dropIfExists('colors');
        Schema::dropIfExists('sizes');
    }
};
