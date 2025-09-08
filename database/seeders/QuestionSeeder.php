<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Tile;       // 「App\Models\Tile」から「App\Tile」に修正
use App\Models\Question;   // 「App\Models\Question」から「App\Question」に修正
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\File;
class QuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
            Question::truncate();
        Schema::enableForeignKeyConstraints(); // テーブルを一旦空にする

        $path = database_path('seeders/json/questions'); // 問題ファイルまでのパス
        $ma_jyancourse = 'ma-jyancourse';//麻雀問題のファイルパス
        $eikencourse = 'eikencourse';//英検問題のファイルパス

        //役の名前、翻数（仮）、構造を入れたjsonファイル名
        $ma_jyans = [
            ['day' => 1, 'course_id' => 1, 'difficulty_id' => 1, 'question' => 'day1.json'],
            ['day' => 2, 'course_id' => 1, 'difficulty_id' => 1, 'question' => 'day2.json'],
            ['day' => 3, 'course_id' => 1, 'difficulty_id' => 1, 'question' => 'day3.json'],
            ['day' => 4, 'course_id' => 1, 'difficulty_id' => 1, 'question' => 'day4.json'],
            ['day' => 5, 'course_id' => 1, 'difficulty_id' => 1, 'question' => 'day5.json'],
            ['day' => 6, 'course_id' => 1, 'difficulty_id' => 1, 'question' => 'day6.json'],
            ['day' => 7, 'course_id' => 1, 'difficulty_id' => 1, 'question' => 'day7.json'],
            ['day' => 8, 'course_id' => 1, 'difficulty_id' => 1, 'question' => 'day8.json'],
            ['day' => 9, 'course_id' => 1, 'difficulty_id' => 1, 'question' => 'day9.json'],
            ['day' => 10, 'course_id' => 1, 'difficulty_id' => 1, 'question' => 'day10.json'],
            ['day' => 11, 'course_id' => 1, 'difficulty_id' => 1, 'question' => 'day11.json'],
            ['day' => 12, 'course_id' => 1, 'difficulty_id' => 1, 'question' => 'day12.json'],
            ['day' => 13, 'course_id' => 1, 'difficulty_id' => 1, 'question' => 'day13.json'],
        ];

        $eikens = [
            ['day' => 1, 'course_id' => 2, 'difficulty_id' => 2, 'question' => 'day1.json'],
            ['day' => 2, 'course_id' => 2, 'difficulty_id' => 2, 'question' => 'day2.json'],
            ['day' => 3, 'course_id' => 2, 'difficulty_id' => 2, 'question' => 'day3.json'],
            ['day' => 4, 'course_id' => 2, 'difficulty_id' => 2, 'question' => 'day4.json'],
            ['day' => 5, 'course_id' => 2, 'difficulty_id' => 2, 'question' => 'day5.json'],
            ['day' => 6, 'course_id' => 2, 'difficulty_id' => 2, 'question' => 'day6.json'],
            ['day' => 7, 'course_id' => 2, 'difficulty_id' => 2, 'question' => 'day7.json'],
            ['day' => 8, 'course_id' => 2, 'difficulty_id' => 2, 'question' => 'day8.json'],
            ['day' => 9, 'course_id' => 2, 'difficulty_id' => 2, 'question' => 'day9.json'],
            ['day' => 10, 'course_id' => 2, 'difficulty_id' => 2, 'question' => 'day10.json'],
            ['day' => 11, 'course_id' => 2, 'difficulty_id' => 2, 'question' => 'day11.json'],
            ['day' => 12, 'course_id' => 2, 'difficulty_id' => 2, 'question' => 'day12.json'],
            ['day' => 13, 'course_id' => 2, 'difficulty_id' => 2, 'question' => 'day13.json'],
        ];




        foreach ($ma_jyans as $ma_jyan) {
            $question = File::get($path . '/' . $ma_jyancourse . '/' . $ma_jyan['question']);//ファイルまでのパス生成
            
            Question::create([
                'day' => $ma_jyan['day'],
                'course_id' => $ma_jyan['course_id'],
                'difficulty_id' => $ma_jyan['difficulty_id'],
                'question' => $question,
            ]);
        }
        foreach ($eikens as $eiken) {
            $question = File::get($path . '/' . $eikencourse . '/' . $eiken['question']);//ファイルまでのパス生成
            
            Question::create([
                'day' => $eiken['day'],
                'course_id' => $eiken['course_id'],
                'difficulty_id' => $eiken['difficulty_id'],
                'question' => $question,
            ]);
        }

    }
}

