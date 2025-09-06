<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Difficulty;
use Illuminate\Support\Facades\File;

class DifficultySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        Schema::disableForeignKeyConstraints();
            Difficulty::truncate();
        Schema::enableForeignKeyConstraints(); // テーブルを一旦空にする

        $path = database_path('seeders/json/choice');
        $difficulties=[
            ['course_id' => 1, 'name' => '英検3級相当', 'description' => '英検３級相当の問題です', 'choice' => 'choice1.json'],
            ['course_id' => 2, 'name' => '麻雀初級', 'description' => '麻雀の初級問題です', 'choice' => 'choice2.json'],
        ];

        foreach ($difficulties as $difficulty) {
            $choice = File::get($path . '/' . $difficulty['choice']);
            Difficulty::create([
                'course_id' => $difficulty['course_id'],
                'name' => $difficulty['name'],
                'description' => $difficulty['description'],
                'choice' => $choice
            ]);
        }
    }
}
