<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Course;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        Schema::disableForeignKeyConstraints();
            Course::truncate();
        Schema::enableForeignKeyConstraints(); // テーブルを一旦空にする

        $courses=[
            ['name' => '英検コース', 'category' => 'language', 'description' => '英検用の単語が学べます', 'is_public' => 1],
            ['name' => '麻雀コース', 'category' => 'game', 'description' => '麻雀について学べます', 'is_public' => 1]
        ];

        foreach ($courses as $course) {
            Course::create([
                'name' => $course['name'],
                'category' => $course['category'],
                'description' => $course['description'],
                'is_public' => $course['is_public']
            ]);
        }
    }
}
