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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('order_id');
            $table->bigInteger('product_id');
            $table->integer('quantity')->default(0);
            $table->double('price',8,2);
            $table->double('total_price',8,2);
            $table->string('color')->nullable();
            $table->string('size')->nullable();
            $table->boolean('downloadable')->nullable()->default(false);
            $table->integer('download_limit')->nullable()->default(0);
            $table->integer('downloads')->nullable()->default(0);
            $table->string('download_file')->nullable();
            $table->enum('access', ['revoked','granted'])->nullable()->default('granted');
            $table->boolean('rated')->nullable()->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
