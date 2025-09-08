<?php
// DatabaseSeeder.php - 実行順序を修正
namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            // 基本データから先に作成
            UserSeeder::class,           // ユーザーを先に作成
            CategorySeeder::class,       // カテゴリーを作成
            QuestionSeeder::class,       // 質問を作成（カテゴリー依存）
            ChoiceSeeder::class,         // 選択肢を作成（質問依存）
            // UserTileSeeder::class,       // ユーザーの持ち牌（ユーザー・牌依存）
            // UserDeckSeeder::class,       // ユーザーデッキ（ユーザー依存）
            // UserDeckTileSeeder::class,   // デッキ内容（デッキ・ユーザー持ち牌依存）
        ]);
    }
}