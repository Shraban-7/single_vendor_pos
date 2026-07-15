<?php

use App\Enums\SaleStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sale_status_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sale_id')->index();
            $table->enum('status', array_map(fn($t) => $t->value, SaleStatus::cases()))->nullable();
            $table->text('comment')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sale_status_histories');
    }
};
