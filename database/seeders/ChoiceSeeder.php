<?php
// database/seeders/ChoiceSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Question;
use App\Models\Choice;

class ChoiceSeeder extends Seeder
{
    public function run(): void
    {
        $questions = Question::all();

        foreach ($questions as $question) {
            // 各質問に対して4つの選択肢を作成
            Choice::factory()->count(4)->create([
                'question_id' => $question->id,
                'is_correct' => false, // デフォルトはfalseにする
            ]);

            // 正解が必ず1つはあるように調整
            $choices = Choice::where('question_id', $question->id)->get();
            $correct = $choices->random();
            $correct->is_correct = true; // 正解フラグをtrueに
            $correct->save();
        }
    }
}
