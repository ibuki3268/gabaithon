<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Question;

class QuestionSeeder extends Seeder
{
    public function run(): void
    {
        // 既存のカテゴリーを取得
        $categories = Category::all();

        if ($categories->isEmpty()) {
            $this->command->info('カテゴリーが存在しません。CategorySeederを先に実行してください。');
            return;
        }

        // 各カテゴリーにランダムで質問を作成
        foreach ($categories as $category) {
            Question::factory()->count(3)->create([
                'category_id' => $category->id,
            ]);
        }
    }
}
