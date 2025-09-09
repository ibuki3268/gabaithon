<?php
// User_deck_tiles.php - 関連性を追加
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User_deck_tiles extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_deck_id',
        'tile_id',
        'count',
    ];

    // ユーザーデッキとの関連 (多対1)
    public function userDeck()
    {
        return $this->belongsTo(User_decks::class, 'user_deck_id');
    }

    // 牌との関連 (多対1)
    public function tile()
    {
        return $this->belongsTo(Tile::class);
    }
}