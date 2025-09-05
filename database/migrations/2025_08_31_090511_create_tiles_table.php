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
        Schema::create('tiles', function (Blueprint $table) {
            $table->id(); // 牌を区別するためのID
            $table->string('name'); // 牌の名前
            $table->string('type');
            $table->integer('num')->nullable();
            $table->string('image_path')->nullable();
            $table->timestamps(); // 作成日時など
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tiles');
    }
};
