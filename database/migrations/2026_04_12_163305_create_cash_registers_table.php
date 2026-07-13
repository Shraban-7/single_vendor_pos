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
        Schema::create('cash_registers', function (Blueprint $table) {
            $table->id();
            $table->decimal('opening_amount',11,2)->nullable();
            $table->decimal('sales',15,2)->default(0);
            $table->decimal('expenses',15,2)->default(0);
            $table->decimal('sale_returns',15,2)->default(0);
            $table->decimal('difference',15,2)->default(0);
            $table->decimal('closing_amount',11,2)->nullable();
            $table->timestamp('opened_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_registers');
    }
};
