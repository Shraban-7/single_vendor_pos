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
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('file_name');
            $table->string('original_name');
            $table->string('file_path');
            $table->string('mime_type', 100);
            $table->unsignedBigInteger('file_size'); //Size in bytes
            $table->enum('file_type', array_map(fn($t) => $t->value, \App\Enums\MediaType::cases()));
            $table->string('model_type', 100)->nullable(); //Product, Customer, etc.
            $table->unsignedBigInteger('model_id')->nullable();
            $table->string('alt_text')->nullable();
            $table->timestamps();
            $table->index('file_type');
            $table->index(['model_type', 'model_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};
