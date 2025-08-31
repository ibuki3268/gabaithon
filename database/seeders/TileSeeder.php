<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; // この行を追加

class TileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('tiles')->insert([
            [
                'title' => '英単語 Day1',
                'description' => '基本的な動詞を学習します。',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => '英単語 Day2',
                'description' => '日常で使う名詞を学習します。',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => '英単語 Day3',
                'description' => '便利な形容詞を学習します。',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}