<?php

use App\Enums\Gender;
use App\Enums\UserRole;
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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone')->unique();
            $table->string('email')->nullable()->unique();

            $table->string('password')->nullable();
            $table->enum('role', UserRole::values())->default(UserRole::CUSTOMER->value);
            $table->string('image')->nullable();
            $table->enum('gender', Gender::values())->nullable();
            $table->date('date_of_birth')->nullable();
            
            $table->boolean('is_active')->default(true);
            $table->timestamp('phone_verified_at')->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->timestamp('last_seen')->nullable();
            $table->string('last_login_ip')->nullable();

            $table->rememberToken();
            $table->softDeletes();
            $table->timestamps();
        });
    }
    
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
