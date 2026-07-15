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
        Schema::create('backup_history', function (Blueprint $table) {
            $table->id();
            $table->enum('backup_type', array_map(fn($t) => $t->value, \App\Enums\BackupType::cases()));
            $table->string('file_name');
            $table->string('file_path')->nullable();
            $table->unsignedBigInteger('file_size')->nullable(); //Size in bytes
            $table->enum('status', array_map(fn($t) => $t->value, \App\Enums\BackupStatus::cases()))->default(\App\Enums\BackupStatus::PENDING->value);
            $table->text('error_message')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('backup_history');
    }
};
