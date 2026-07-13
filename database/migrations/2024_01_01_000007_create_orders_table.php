<?php

use App\Enums\DeliveryZone;
use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique(); // SF2026011901
            $table->unsignedBigInteger('user_id')->nullable(); // Nullable for guest orders
            $table->unsignedBigInteger('coupon_id')->nullable();

            // Order Status
            $table->string('status')->default(OrderStatus::PENDING->value);

            // Payment Info
            $table->string('payment_method')->default(PaymentMethod::COD->value);
            $table->string('payment_status')->default(PaymentStatus::PENDING->value);
            $table->string('payment_method_name')->nullable();
            $table->string('payment_id')->nullable();
            $table->timestamp('paid_at')->nullable();

            // Amounts
            $table->decimal('subtotal', 10, 2);
            $table->decimal('shipping_cost', 10, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('total', 10, 2);

            // Shipping Address
            $table->string('shipping_name')->nullable();
            $table->string('shipping_phone')->nullable();
            $table->string('shipping_email')->nullable();
            $table->string('shipping_district')->nullable();
            $table->string('shipping_city')->nullable();
            $table->text('shipping_address')->nullable();
            $table->string('shipping_postal_code')->nullable();
            $table->string('delivery_zone')->default(DeliveryZone::OUTSIDE_DHAKA->value);

            // Additional Info
            $table->text('notes')->nullable(); // Customer notes
            $table->text('admin_notes')->nullable(); // Internal notes
            $table->string('tracking_number')->nullable();
            $table->string('courier')->nullable(); // Pathao, Steadfast, etc.

            // Timestamps
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->string('cancellation_reason')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'status']);
            $table->index(['order_number']);
            $table->index(['status', 'created_at']);
            $table->index(['payment_status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
