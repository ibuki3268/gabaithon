<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Question extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'questions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    // Seederでの利用に合わせて、一括代入可能な属性を更新
    protected $fillable = [
        'day',
        'course_id',
        'difficulty_id',
        'question',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'question' => 'array', // 'question'カラムをJSONから配列へ自動変換
    ];

    /**
     * この問題が属する牌（Tile）を取得する (多対1)
     * 注意: このリレーションを機能させるには 'questions' テーブルに 'tile_id' カラムが必要です。
     */
    public function tile(): BelongsTo
    {
        return $this->belongsTo(Tile::class);
    }

    /**
     * 一つの難易度に属する
     */
    public function difficulty(): BelongsTo
    {
        return $this->belongsTo(Difficulty::class);
    }

    /**
     * 関連的にコースを取得
     */
    public function course()
    {
        // difficultyリレーションを経由してCourseモデルを取得します
        return $this->difficulty->course;
    }
}
