<?php

namespace Database\Factories;

use App\Models\Choice;
use App\Models\Question;
use Illuminate\Database\Eloquent\Factories\Factory;

class ChoiceFactory extends Factory
{
    protected $model = Choice::class;

    public function definition(): array
    {
        return [
            'question_id' => Question::factory(), // question_id を自動生成
            'text' => $this->faker->sentence(),
            'is_correct' => false, // typo 修正、デフォルトは false
        ];
    }
}
