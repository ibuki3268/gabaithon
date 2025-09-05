<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Progress extends Model
{
    use HasFactory;
    // 進捗は1ユーザーに属する
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