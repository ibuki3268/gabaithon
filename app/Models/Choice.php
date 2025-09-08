<?php
// Choice.php - 関連性を追加
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Choice extends Model
{
    use HasFactory;

    protected $fillable = [
        'question_id',
        'text',
        'is_correct',
    ];

    // 質問との関連 (多対1)
    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}