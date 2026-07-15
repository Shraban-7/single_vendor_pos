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
        Schema::create('exchange_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sale_return_id'); //Link to the return/exchange transaction
            $table->unsignedBigInteger('product_id');
            $table->string('name');
            $table->decimal('quantity', 12, 2);
            $table->decimal('unit_price', 12, 2); //Selling price of exchange item
            $table->decimal('cost_price', 12, 2)->nullable();
            $table->decimal('exchange_value', 12, 2)->nullable();
            $table->decimal('subtotal', 12, 2);
            $table->decimal('total', 12, 2);
            $table->timestamps();
            $table->index('sale_return_id');
            $table->index('product_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exchange_items');
    }
};
