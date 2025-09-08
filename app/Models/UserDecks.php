<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDeck extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'deck_id',
        'count',
    ];

    // リレーション
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function deck()
    {
        return $this->belongsTo(Deck::class);
    }
}
