<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Difficulty extends Model
{
    use HasFactory;

    protected $table = 'questions'; // テーブル名
    protected $casts = [
        'content' => 'array', // JSONカラムを自動で配列に変換
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
    public function questions()
    {
        return $this->hasMany(Question::class)->orderBy('order');
    }
}