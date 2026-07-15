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
        Schema::create('daily_reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->date('report_date')->unique();
            $table->decimal('total_sales', 12, 2)->default(0.00);
            $table->unsignedInteger('total_sales_count')->default(0);
            $table->decimal('total_purchases', 12, 2)->default(0.00);
            $table->unsignedInteger('total_purchases_count')->default(0);
            $table->decimal('total_expenses', 12, 2)->default(0.00);
            $table->unsignedInteger('total_expenses_count')->default(0);
            $table->decimal('total_payments_received', 12, 2)->default(0.00);
            $table->decimal('total_payments_made', 12, 2)->default(0.00);
            $table->decimal('net_profit', 12, 2)->default(0.00);
            $table->timestamps();

            $table->index('report_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_reports');
    }
};
