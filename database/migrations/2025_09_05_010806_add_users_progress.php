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
        Schema::table('users', function (Blueprint $table) {
            //
            $table->foreignId('progress_id');//進捗管理テーブルのID追加
            $table->integer('points')->default(0);//点棒管理用（ガチャ石）
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
            $table->dropForeign(['progress_id']);
            $table->dropColumn('progress_id');
            $table->dropColumn('points');
        });
    }
};
