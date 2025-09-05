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
            $table->id(); // 問題を区別するためのID
            //$table->foreignId('course_id')->constrained()->onDelete('cascade'); // どの牌に属する問題か
            //$table->foreignId('difficulty_id')->constrained()->onDelete('cascade');
            $table->integer('day');
            $table->json('question');
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
