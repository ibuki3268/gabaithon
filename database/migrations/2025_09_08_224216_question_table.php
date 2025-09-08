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
        Schema::create('questions', function (Blueprint $table) {
            $table->id(); // 牌を区別するためのID
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade'); // カテゴリーID
            $table->string('question'); // 問題本文
            $table->timestamps(); // 作成日時など
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
