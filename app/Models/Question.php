<?php
// Question.php - 関連性を追加
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'tile_id',
        'question',
    ];

    // カテゴリーとの関連 (多対1)
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // 選択肢との関連 (1対多)
    public function choices()
    {
        return $this->hasMany(Choice::class);
    }

    // 牌との関連 (多対1)
    public function tile()
    {
        return $this->belongsTo(Tile::class);
    }
}