<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\UserDeck;
use App\Models\Deck;

class UserDeckSeeder extends Seeder
{
    public function run()
    {
        $users = User::all();
        $decks = Deck::all();

        foreach ($users as $user) {
            // 各ユーザーに2つのデッキを割り当てる
            $decksForUser = $decks->random(2); // 既存のデッキからランダムに2つ選ぶ

            foreach ($decksForUser as $deck) {
                UserDeck::factory()->create([
                    'user_id' => $user->id,
                    'deck_id' => $deck->id,
                ]);
            }
        }
    }
}
