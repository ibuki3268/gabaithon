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
        Schema::table('tiles', function (Blueprint $table) {
            //
            $table->dropColumn('description');
            $table->renameColumn('title', 'name');
            $table->string('type');
            $table->integer('num')->nullable();
            $table->string('image_path')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tiles', function (Blueprint $table) {
            //
            $table->text('description')->nullable();
            $table->renameColumn('name', 'title');
            $table->dropColumn('type');
            $table->dropColumn('num');
            $table->dropColumn('image_path');
        });
    }
};
