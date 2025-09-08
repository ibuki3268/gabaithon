<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Tile;
use App\Models\UserTile;

class UserTileSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $tiles = Tile::all();

        foreach ($users as $user) {
            foreach ($tiles as $tile) {
                UserTile::factory()->create([
                    'user_id' => $user->id,
                    'tile_id' => $tile->id,
                    'count'   => rand(0, 4),
                ]);
            }
        }
    }
}
