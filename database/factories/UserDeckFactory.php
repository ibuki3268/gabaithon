<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\UserDeck;
use App\Models\User;
use App\Models\Deck;

class UserDeckFactory extends Factory
{
    protected $model = UserDeck::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),  // 新しいユーザーを作る場合
            'deck_id' => Deck::factory(),  // 新しいデッキを作る場合
            'count' => $this->faker->numberBetween(1, 5),
        ];
    }
}
