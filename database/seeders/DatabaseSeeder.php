<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // �ȉ���2�s��ǉ����܂�
        $this->call([
            TileSeeder::class,
            QuizSeeder::class, // QuizSeeder���Ăяo���悤�ɒǉ�
        ]);
    }
}
