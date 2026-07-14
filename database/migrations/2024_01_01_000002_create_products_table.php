<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->unsignedBigInteger('unit_id')->nullable();
            $table->string('sku', 100)->unique();
            $table->string('barcode', 100)->nullable();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->decimal('cost_price', 12, 2)->default(0.00);
            $table->decimal('selling_price', 12, 2)->default(0.00);
            $table->decimal('wholesale_price', 12, 2)->nullable();
            $table->decimal('stock_in', 12, 2)->default(0.00);
            $table->decimal('stock_out', 12, 2)->default(0.0);
            $table->decimal('stock_quantity', 12, 2)->default(0.00);
            $table->decimal('stock_alert_quantity', 12, 2)->nullable();
            $table->decimal('vat_rate', 5, 2)->default(0.00);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_returnable')->default(true);
            $table->softDeletes();
            $table->timestamps();

            $table->index('barcode');
            $table->index('category_id');
            $table->index('stock_quantity');
            $table->index('stock_alert_quantity');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
