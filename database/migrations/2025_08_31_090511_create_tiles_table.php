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
            $table->string('title'); // 牌のタイトル（例：「英単語 Day1」）
            $table->text('description')->nullable(); // 牌の簡単な説明（任意）
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
