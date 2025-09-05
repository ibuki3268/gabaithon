<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // usersテーブルに progress_id 外部キーを追加
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('progress_id')->nullable()->constrained('progress')->onDelete('set null');
        });

        // questionsテーブルに外部キーを追加
        Schema::table('questions', function (Blueprint $table) {
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
            $table->foreignId('difficulty_id')->constrained('progress')->onDelete('cascade');
        });
        
        // 他に必要な外部キーがあれば追加
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['progress_id']);
            $table->dropColumn('progress_id');
        });

        Schema::table('questions', function (Blueprint $table) {
            $table->dropForeign(['course_id']);
            $table->dropForeign(['difficulty_id']);
            $table->dropColumn('course_id');
            $table->dropColumn('difficulty_id');
        });
    }
};