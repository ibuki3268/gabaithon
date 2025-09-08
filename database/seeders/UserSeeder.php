<?php
// UserSeeder.php - 不足していたSeederを追加
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->count(10)->create();
    }
}