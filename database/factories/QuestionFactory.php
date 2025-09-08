<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Question;
use App\Models\Category;

class QuestionFactory extends Factory
{
    protected $model = Question::class;

    public function definition(): array
    {
        return [
            'category_id' => Category::factory(), // 関連するカテゴリーを自動生成
            'question' => $this->faker->sentence(), // ランダムな文章
        ];
    }
}
