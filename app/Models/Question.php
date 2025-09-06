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
     * ?��?��?��̖�肪?��?��?��?��?��?��v?��iTile?��j?��?��?��擾?��?��?��?�� (?��?��?��?��1)
     */
    public function tile(): BelongsTo
    {
        return $this->belongsTo(Tile::class);
    }

    /**
     * ?��?��?��̖�肪?��?��?��I?��?��?��?��?��iChoices?��j?��?��?��擾?��?��?��?�� (1?��Α�)
     */


    protected $table = 'questions'; // �?ーブル�?
    protected $casts = [
        'content' => 'array', // JSONカラ�?を�?�動で配�?�に変換
    ];

    public function difficulty()//一つの難易度に属す�?
    {
        return $this->belongsTo(Difficulty::class);
    }
    // 間接�?にコースを取�?
    public function course()
    {
        return $this->difficulty->course;
    }
}