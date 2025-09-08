<?php
// database/migrations/xxxx_add_newstudy_to_progress_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('progress', function (Blueprint $table) {
            $table->boolean('newstudy')->default(true);
            // 他に追加したいカラムがあればここに
        });
    }

    public function down()
    {
        Schema::table('progress', function (Blueprint $table) {
            $table->dropColumn('newstudy');
        });
    }
};