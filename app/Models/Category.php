<?php
// Category.php - 関連性を追加
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'categoryname',
    ];

    // 質問との関連 (1対多)
    public function questions()
    {
        return $this->hasMany(Question::class);
    }
}