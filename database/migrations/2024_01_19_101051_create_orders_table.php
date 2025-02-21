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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id');
            $table->string('name');
            $table->string('image');
            $table->string('code');
            $table->string('payment_id')->nullable();
            $table->string('payment_status');
            $table->string('payment_method')->nullable();
            $table->double('amount',8,2);
            $table->double('sub_total',8,2);
            $table->double('discount_amount',8,2);
            $table->enum('status',['pending','confirmed','processing','completed','cancelled'])->default('pending');
            $table->string('shipping')->nullable();
            $table->string('pickup_location')->nullable();
            $table->integer('quantity');
            $table->string('coupon_code')->nullable();
            $table->string('coupon_type')->nullable();
            $table->double('shipping_amount',8,2)->nullable();
            $table->double('tax_amount',8,2)->default(0.0)->nullable();
            $table->boolean('is_confirmed')->nullable()->default(false);
            $table->boolean('downloadable')->nullable()->default(false);
            $table->enum('download_access',['revoked','granted'])->nullable()->default('granted');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
