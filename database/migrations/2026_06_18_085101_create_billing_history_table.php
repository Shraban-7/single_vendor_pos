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
        Schema::create('billing_history', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('subscription_id')->nullable();
            $table->string('invoice_number', 100)->unique();
            $table->dateTime('billing_date');
            $table->date('due_date')->nullable();
            $table->decimal('amount', 10, 2);
            $table->decimal('tax_amount', 10, 2)->default(0.00);
            $table->decimal('total_amount', 10, 2);
            $table->enum('status', array_map(fn($t) => $t->value, \App\Enums\BillingStatus::cases()))->default(\App\Enums\BillingStatus::PENDING->value);
            $table->string('payment_method', 50)->nullable();
            $table->string('transaction_id', 255)->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->text('notes')->nullable();
            $table->string('invoice_pdf')->nullable();
            $table->timestamps();
            $table->index('status');
            $table->index('billing_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('billing_history');
    }
};
