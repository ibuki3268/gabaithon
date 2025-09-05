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
        Schema::table('choices', function (Blueprint $table) {
            //
            Schema::dropIfExists('choices');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('choices', function (Blueprint $table) {
            //
            $table->id(); // 選択肢を区別するためのID
            $table->foreignId('question_id')->constrained()->onDelete('cascade'); // どの問題に属する選択肢か
            $table->string('text'); // 選択肢のテキスト
            $table->boolean('is_correct')->default(false); // この選択肢が正解かどうか
            $table->timestamps();
        });
    }
};
