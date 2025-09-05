<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Tile;       // 「App\Models\Tile」から「App\Tile」に修正
use App\Models\Question;   // 「App\Models\Question」から「App\Question」に修正

class QuizSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // まず、クイズを追加したい親となる牌（Tile）を取得します
        $tile1 = Tile::where('title', '英単語 Day1')->first();

        // もし「英単語 Day1」の牌が見つかった場合のみ、処理を続けます
        if ($tile1) {
            // 既にこの問題が存在しないかチェックします
            if (!Question::where('question_text', '「走る」を意味する英単語は次のうちどれ？')->exists()) {
                // 1問目の問題を作成します
                $question1 = Question::create([
                    'tile_id' => $tile1->id,
                    'question_text' => '「走る」を意味する英単語は次のうちどれ？'
                ]);

                // 1問目の問題に対する選択肢を4つ作成します
                $question1->choices()->createMany([
                    ['text' => 'eat', 'is_correct' => false],
                    ['text' => 'walk', 'is_correct' => false],
                    ['text' => 'run', 'is_correct' => true], // これが正解
                    ['text' => 'sleep', 'is_correct' => false],
                ]);
            }
        }
    }
}

