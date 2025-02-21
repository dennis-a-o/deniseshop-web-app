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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug');
            $table->string('sku')->nullable();
            $table->text('description')->nullable();
            $table->text('description_summary')->nullable();
            $table->string('image')->nullable();
            $table->json('gallery')->nullable();
            $table->double('price', 8, 2);
            $table->double('sale_price', 8, 2)->nullable()->default(0.00);
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->integer('quantity')->nullable()->default(0);
            $table->integer('sold')->nullable()->default(0);
            $table->integer('brand_id')->nullable()->default(0);
            $table->boolean('is_featured')->nullable()->default(false);
            $table->enum('stock_status', ['in_stock', 'out_stock'])->nullable();
            $table->enum('status', ['published', 'draft', 'pending'])->nullable()->default('draft');
            $table->bigInteger('views')->nullable();
            $table->enum('type', ['internal','external']);
            $table->string('url')->nullable();
            $table->string('button_text')->nullable();
            $table->boolean('downloadable')->nullable()->default(false);
            $table->integer('download_limit')->nullable()->default(0);
            $table->integer('download_expiry')->nullable()->default(0);
            $table->string('download_file')->nullable();
            $table->double('weight',8,2)->nullable();
            $table->double('length',8,2)->nullable();
            $table->double('width',8,2)->nullable();
            $table->double('height',8,2)->nullable();
            $table->integer('ram')->nullable();
            $table->integer('rom')->nullable();
            $table->integer('screen_size')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
