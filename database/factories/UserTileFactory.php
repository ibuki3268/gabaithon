<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\UserTile;
use App\Models\User;
use App\Models\Tile;

class UserTileFactory extends Factory
{
    protected $model = UserTile::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(), // 新しいユーザーを作る場合
            'tile_id' => Tile::factory(), // 新しい牌を作る場合
            'count'   => $this->faker->numberBetween(0, 4),
        ];
    }
}
