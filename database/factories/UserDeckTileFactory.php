<?php
// UserDeckTileFactory.php - モデル名を修正
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User_deck_tiles; // 正しいモデル名
use App\Models\User_decks;      // 正しいモデル名
use App\Models\Tile;

class UserDeckTileFactory extends Factory
{
    protected $model = User_deck_tiles::class; // 正しいモデル名

    public function definition(): array
    {
        return [
            'user_deck_id' => User_decks::factory(), // 正しいモデル名
            'tile_id' => Tile::factory(),
            'count' => $this->faker->numberBetween(1, 4),
        ];
    }
}