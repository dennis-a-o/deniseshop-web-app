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
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('order_id');
            $table->bigInteger('user_id');
            $table->double('weight', 8,2);
            $table->bigInteger('shipment_id')->nullable();
            $table->Integer('rating')->nullable();
            $table->string('note')->nullable();
            $table->string('status')->default('pending')->nullable();
            $table->double('cod_amount')->nullable();
            $table->string('cod_status')->default('pending')->nullable();
            $table->double('price',8,2)->nullable();
            $table->string('tracking_id')->nullable();
            $table->string('shipping_company_name')->nullable();
            $table->string('tracking_link')->nullable();
            $table->datetime('estimate_date_shipped')->nullable();
            $table->datetime('date_shipped')->nullable();
            $table->text('label_url')->nullable();
            $table->mediumText('metadata')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};
