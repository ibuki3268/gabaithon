<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Progress extends Model
{
    use HasFactory;

    // fillable に progresses と status を追加
    protected $fillable = [
        'user_id',
        'day',
        'course_id',
        'difficulty_id',
        'progresses',    // ← 追加
        'yaku_id',
        'status',        // ← 追加
    ];

    // JSON を配列として扱うためのキャスト（おすすめ）
    protected $casts = [
        'progresses' => 'array',
    ];

    // リレーション
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function difficulty()
    {
        return $this->belongsTo(Difficulty::class);
    }

    public function yaku()
    {
        return $this->belongsTo(Yaku::class);
    }
}