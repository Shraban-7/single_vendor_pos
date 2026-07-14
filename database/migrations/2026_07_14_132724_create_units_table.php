<?php

use App\Enums\ProductUnitType;
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
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->unique(); //kg, liter, piece, box, etc.
            $table->string('name_bn', 50)->nullable();
            $table->string('short_name', 20);
            $table->string('short_name_bn', 20)->nullable();
            $table->enum('type', array_map(fn($t) => $t->value, ProductUnitType::cases()))->default(ProductUnitType::QUANTITY->value);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};
