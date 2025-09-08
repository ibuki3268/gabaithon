<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserTile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'tile_id',
        'count',
    ];

    // ユーザーとの関連 (多対1)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 牌との関連 (多対1)
    public function tile()
    {
        return $this->belongsTo(Tile::class);
    }
}
