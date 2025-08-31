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
        Schema::create('choices', function (Blueprint $table) {
            $table->id(); // 選択肢を区別するためのID
            $table->foreignId('question_id')->constrained()->onDelete('cascade'); // どの問題に属する選択肢か
            $table->string('text'); // 選択肢のテキスト（例：「東京」）
            $table->boolean('is_correct')->default(false); // この選択肢が正解かどうか (true/false)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('choices');
    }
};
