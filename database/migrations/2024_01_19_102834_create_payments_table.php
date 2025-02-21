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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id');
            $table->string('currency');
            $table->string('status',100)->nullable()->default('pending');
            $table->string('transaction_id');
            $table->string('payment_channel')->nullable();
            $table->bigInteger('payment_method_id')->nullable();
            $table->string('description')->nullable();
            $table->double('amount',8,2);
            $table->bigInteger('order_id');
            $table->double('refund_amount',8,2)->nullable()->default(0.0);
            $table->mediumText('refund_note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
