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
        Schema::table('questions', function (Blueprint $table) {
            //
            $table->dropForeign(['tile_id']);
            $table->dropColumn('tile_id');

            $table->foreignId('course_id');
            $table->foreignId('difficulty_id');

            $table->dropColumn('question_text');

            $table->integer('day');
            $table->json('question');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            //
            $table->foreignId('tile_id')->constrained()->onDelete('cascade');

            $table->dropForeign(['course_id']);
            $table->dropColumn('course_id');
            $table->dropForeign(['difficulty_id']);
            $table->dropColumn('difficulty_id');

            $table->text('question_text'); // 問題文
            
            $table->dropColumn('day');
            $table->dropColumn('question');
        });
    }
};
