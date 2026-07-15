<?php

use App\Enums\DiscountType;
use App\Enums\PaymentStatus;
use App\Enums\ReturnStatus;
use App\Enums\SaleStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->unsignedBigInteger('employee_id')->nullable();
            $table->string('invoice_number', 100)->unique();
            $table->date('sale_date');
            $table->date('due_date')->nullable();
            $table->enum('status', array_map(fn($t) => $t->value, SaleStatus::cases()))->default(SaleStatus::COMPLETED->value);
            $table->enum('payment_status', array_map(fn($t) => $t->value, PaymentStatus::cases()))->default(PaymentStatus::UNPAID->value);
            $table->decimal('subtotal', 12, 2)->default(0.00);
            $table->decimal('vat_rate', 4, 2)->default(0.00);
            $table->decimal('tax_amount', 12, 2)->default(0.00);
            $table->enum('discount_type', array_map(fn($t) => $t->value, DiscountType::cases()))->nullable();
            $table->decimal('discount_value', 12, 2)->default(0.00)->nullable();
            $table->decimal('discount_amount', 12, 2)->default(0.00);
            $table->decimal('shipping_cost', 12, 2)->default(0.00);
            $table->decimal('total_amount', 12, 2)->default(0.00);
            $table->decimal('payable', 12, 2)->default(0.00);
            $table->decimal('paid_amount', 12, 2)->default(0.00);
            $table->decimal('due_amount', 12, 2)->default(0.00);
            $table->decimal('cash_received', 12, 2)->default(0.00);
            $table->decimal('change_amount', 12, 2)->default(0.00);
            $table->timestamp('paid_at')->nullable();
            $table->decimal('profit_amount', 12, 2)->default(0.00);
            $table->string('payment_method', 50)->nullable();
            $table->text('payment_note')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('has_return')->default(false);
            $table->enum('return_status', array_map(fn($t) => $t->value, ReturnStatus::cases()))->default(ReturnStatus::NONE->value); //Return status
            $table->decimal('returned_amount', 12, 2)->default(0.00);
            $table->softDeletes();
            $table->timestamps();
            $table->index('sale_date', 'idx_sale_date');
            $table->index('status');
            $table->index('payment_status');
            $table->index('customer_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
