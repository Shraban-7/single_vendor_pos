<?php

use App\Enums\PaymentStatus;
use App\Enums\PurchaseStatus;
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
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('supplier_id')->nullable();
            $table->string('purchase_number', 100)->unique();
            $table->dateTime('purchase_date')->index('idx_purchase_date');
            $table->date('due_date')->nullable();
            $table->enum('status', array_map(fn($t) => $t->value, PurchaseStatus::cases()))->default(PurchaseStatus::ORDERED->value);
            $table->enum('payment_status', array_map(fn($t) => $t->value, PaymentStatus::cases()))->default(PaymentStatus::UNPAID->value)->index();
            $table->decimal('subtotal', 12, 2)->default(0.00);
            $table->decimal('paid_amount', 12, 2)->default(0.00);
            $table->decimal('due_amount', 12, 2)->default(0.00);
            $table->string('payment_method', 50)->nullable();
            $table->text('payment_note')->nullable();
            $table->text('notes')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};
