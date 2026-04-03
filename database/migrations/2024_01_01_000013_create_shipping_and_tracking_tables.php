<?php

use App\Enums\NotificationStatus;
use App\Enums\NotificationType;
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
        // Shipping zones and rates
        Schema::create('shipping_zones', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Inside Dhaka, Outside Dhaka
            $table->string('code')->unique(); // inside_dhaka, outside_dhaka
            $table->decimal('shipping_cost', 10, 2);
            $table->decimal('free_shipping_threshold', 10, 2)->nullable(); // Free shipping above this amount
            $table->string('estimated_days'); // 1-2, 3-5
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Districts for address selection
        Schema::create('districts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('bn_name')->nullable(); // Bengali name
            $table->foreignId('shipping_zone_id')->nullable()->constrained()->onDelete('set null');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Order status history/tracking
        Schema::create('order_status_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->string('status');
            $table->text('comment')->nullable();
            $table->string('updated_by')->nullable(); // Admin name or 'system'
            $table->timestamps();

            $table->index(['order_id']);
        });

        // SMS/Notification logs
        Schema::create('notification_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('type', NotificationType::values())->default(NotificationType::SMS->value);
            $table->string('recipient'); // Phone or email
            $table->string('template')->nullable();
            $table->text('message');
            $table->enum('status', NotificationStatus::values())->default(NotificationStatus::PENDING->value);
            $table->text('error_message')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->index(['order_id']);
            $table->index(['status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_logs');
        Schema::dropIfExists('order_status_histories');
        Schema::dropIfExists('districts');
        Schema::dropIfExists('shipping_zones');
    }
};
