<?php
// UserDeckTileSeeder.php - モデル名を修正
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User_decks;      // 正しいモデル名
use App\Models\User_deck_tiles; // 正しいモデル名
use App\Models\UserTile;        // 正しいモデル名（User_tile -> UserTile）

class UserDeckTileSeeder extends Seeder
{
    public function run(): void
    {
        $userDecks = User_decks::all();

        foreach ($userDecks as $deck) {
            $userId = $deck->user_id;

            // ユーザーが持っている牌を取得
            $userTiles = UserTile::where('user_id', $userId)->get(); // 正しいモデル名

            if ($userTiles->isEmpty()) {
                continue;
            }

            // デッキに入れる牌の種類をランダムに選択
            $selectedTiles = $userTiles->random(min(10, $userTiles->count()));

            foreach ($selectedTiles as $userTile) {
                // デッキに入れる枚数を決定
                $count = rand(1, $userTile->count);

                // レコード作成
                User_deck_tiles::create([
                    'user_deck_id' => $deck->id,
                    'tile_id' => $userTile->tile_id,
                    'count' => $count,
                ]);
            }
        }
    }
}