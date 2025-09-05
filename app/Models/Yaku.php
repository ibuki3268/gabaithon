<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Yaku extends Model
{
    use HasFactory;
    protected $table = 'yakus';

    protected $fillable = [
        'name',
        'level',
        'structure',
    ];

    protected $casts = [
        'structure' => 'array', // JSONを配列として扱えるようにする
    ];
}
