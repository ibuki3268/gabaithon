<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Question extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'tile_id',
        'question_text',
    ];


    /**
     * この問題が属する牌（Tile）を取得する (多対1)
     */
    public function tile(): BelongsTo
    {
        return $this->belongsTo(Tile::class);
    }

    /**
     * この問題が持つ選択肢（Choices）を取得する (1対多)
     */
    public function choices(): HasMany
    {
        return $this->hasMany(Choice::class);
    }
}