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
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->enum('type', array_map(fn($t) => $t->value, \App\Enums\PaymentMethodType::cases()));
            $table->string('provider', 100)->nullable(); //Visa, Mastercard, bKash, Nagad, etc.
            $table->string('account_name')->nullable();
            $table->string('account_number', 100)->nullable();
            $table->string('card_last_four', 4)->nullable();
            $table->string('card_brand', 50)->nullable();
            $table->tinyInteger('expiry_month')->nullable();
            $table->smallInteger('expiry_year')->nullable();
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('is_default');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_methods');
    }
};
