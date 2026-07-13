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
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('payable',11,2)->nullable()->after('total');
            $table->decimal('paid',11,2)->nullable()->after('payable');
            $table->decimal('due',11,2)->nullable()->after('paid');
            $table->decimal('cash_received',11,2)->nullable()->after('due');
            $table->decimal('cash_returned',11,2)->nullable()->after('cash_received');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            //
        });
    }
};
