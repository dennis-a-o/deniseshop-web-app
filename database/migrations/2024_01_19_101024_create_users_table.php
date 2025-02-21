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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('email',200);
            $table->string('username',100)->nullable();
            $table->string('phone',100)->nullable();
            $table->string('first_name',200)->nullable();
            $table->string('last_name',200)->nullable();
            $table->string('password');
            $table->string('email_verified_at')->timestamp()->nullable();
            $table->enum('role', ['user','admin', 'manager'])->default('user');
            $table->string('image')->nullable();
            $table->enum('status',['locked', 'activated'])->default('activated');
            $table->rememberToken()->nullable();
            $table->string('last_login')->nullable();
            $table->boolean('accepted_terms')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
