<?php

use App\Enums\AddressType;
use App\Enums\DeliveryZone;
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
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('name'); // Recipient name
            $table->string('phone');
            $table->string('email')->nullable();
            $table->enum('address_type', AddressType::values())->default(AddressType::SHIPPING->value);
            $table->string('district');
            $table->string('city'); // Upazila/Area
            $table->text('address'); // Full address
            $table->string('postal_code')->nullable();
            $table->enum('delivery_zone', DeliveryZone::values())->default(DeliveryZone::OUTSIDE_DHAKA->value);
            $table->boolean('is_default')->default(false);
            $table->timestamps();

            $table->index(['user_id', 'is_default']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
