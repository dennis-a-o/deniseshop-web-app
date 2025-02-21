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
        Schema::create('order_return_items', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('order_return_id');
            $table->bigInteger('order_product_id');
            $table->bigInteger('product_id');
            $table->string('product_name');
            $table->string('product_image');
            $table->integer('qty');
            $table->double('price',8,2);
            $table->double('refund_amount',8,2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_return_items');
    }
};
