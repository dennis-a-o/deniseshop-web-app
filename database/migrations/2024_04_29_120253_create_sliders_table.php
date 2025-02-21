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
        Schema::create('sliders', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('sub_title');
            $table->string('highlight_text');
            $table->string('image');
            $table->mediumText('description')->nullable();
            $table->string('type');
            $table->bigInteger('type_id');
            $table->string('link');
            $table->integer('order')->default(0)->nullable();
            $table->enum('status',['published','pending','draft'])->default('pending');
            $table->string('button_text');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sliders');
    }
};
