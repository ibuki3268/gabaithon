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
        Schema::create('user_deck_tiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_deck_id')->constrained('user_decks')->onDelete('cascade'); // どのデッキか
            $table->foreignId('tile_id')->constrained('tiles')->onDelete('cascade');
            $table->integer('count')->default(0); // デッキに入れた枚数
            $table->timestamps();

            $table->unique(['user_deck_id', 'tile_id']); // デッキ内で同じ牌は1レコードにまとめる
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_deck_tiles');
    }
};


