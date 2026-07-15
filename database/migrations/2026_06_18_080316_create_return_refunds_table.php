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
        Schema::create('return_refunds', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sale_return_id');
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('refund_number', 100)->unique();
            $table->dateTime('refund_date');
            $table->enum('refund_method', array_map(fn($t) => $t->value, \App\Enums\ReturnRefundMethod::cases()));
            $table->decimal('amount', 12, 2);
            $table->string('transaction_id', 255)->nullable(); //Bank/Mobile banking transaction reference
            $table->string('account_number', 100)->nullable(); //Customer account for bank transfer
            $table->string('payment_provider', 100)->nullable(); //bKash, Nagad, Rocket, etc.
            $table->enum('status', array_map(fn($t) => $t->value, \App\Enums\ReturnRefundStatus::cases()))->default(\App\Enums\ReturnRefundStatus::PENDING->value);
            $table->string('receipt_number', 100)->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('processed_by')->nullable();

            $table->timestamps();
            $table->index('refund_date', 'idx_refund_date');
            $table->index('sale_return_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('return_refunds');
    }
};
