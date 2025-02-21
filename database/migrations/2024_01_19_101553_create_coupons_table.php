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
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->mediumText('description')->nullable();
            $table->enum('type',['percent','amount','free_shipping']);
            $table->integer('value');
            $table->integer('user_limit')->default(0)->nullable();
            $table->integer('usage_limit')->default(0)->nullable();
            $table->integer('minimum_spend')->default(0)->nullable();
            $table->integer('maximum_spend')->default(0)->nullable();
            $table->integer('used')->nullable()->default(0);
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->boolean('status')->default(true)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
