<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;
    // 1コースは複数の難易度を持つ
    public function difficulties()
    {
        return $this->hasMany(Difficulty::class);
    }

    // コース内の全質問
    public function questions()
    {
        return $this->hasManyThrough(Question::class, Difficulty::class);
    }

    protected $fillable = ['name', 'description', 'category'];
    
    public function progress()
    {
        return $this->hasMany(Progress::class);
    }
}
