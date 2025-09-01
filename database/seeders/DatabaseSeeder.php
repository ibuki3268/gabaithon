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
        // ˆÈ‰º‚Ì2s‚ğ’Ç‰Á‚µ‚Ü‚·
        $this->call([
            TileSeeder::class,
            QuizSeeder::class, // QuizSeeder‚ğŒÄ‚Ño‚·‚æ‚¤‚É’Ç‰Á
        ]);
    }
}
