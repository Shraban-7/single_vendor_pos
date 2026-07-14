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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->string('payment_number', 100);
            $table->enum('payment_type', array_map(fn($t) => $t->value, \App\Enums\PaymentType::cases()));
            $table->nullableMorphs('reference');
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->unsignedBigInteger('supplier_id')->nullable();
            $table->decimal('amount', 12, 2);
            $table->dateTime('payment_date');
            $table->string('payment_method', 50);
            $table->string('payment_account', 255)->nullable();
            $table->string('transaction_id', 255)->nullable();
            $table->text('notes')->nullable();
            $table->string('receipt_image')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->index('payment_date', 'idx_payment_date');
            $table->index(['reference_type', 'reference_id'], 'idx_reference');
            $table->index('payment_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
