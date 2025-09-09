<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        // 例: 5件のカテゴリーを作成
        Category::factory()->count(5)->create();
    }
}
