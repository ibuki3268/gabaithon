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
     * ?¿½?¿½?¿½Ì–ï¿½è‚ª?¿½?¿½?¿½?¿½?¿½?¿½v?¿½iTile?¿½j?¿½?¿½?¿½æ“¾?¿½?¿½?¿½?¿½ (?¿½?¿½?¿½?¿½1)
     */
    public function tile(): BelongsTo
    {
        return $this->belongsTo(Tile::class);
    }

    /**
     * ?¿½?¿½?¿½Ì–ï¿½è‚ª?¿½?¿½?¿½Â‘I?¿½?¿½?¿½?¿½?¿½iChoices?¿½j?¿½?¿½?¿½æ“¾?¿½?¿½?¿½?¿½ (1?¿½Î‘ï¿½)
     */


    protected $table = 'questions'; // ãƒ?ãƒ¼ãƒ–ãƒ«å?
    protected $casts = [
        'content' => 'array', // JSONã‚«ãƒ©ãƒ?ã‚’è?ªå‹•ã§é…å?—ã«å¤‰æ›
    ];

    public function difficulty()//ä¸€ã¤ã®é›£æ˜“åº¦ã«å±ã™ã‚?
    {
        return $this->belongsTo(Difficulty::class);
    }
    // é–“æ¥çš?ã«ã‚³ãƒ¼ã‚¹ã‚’å–å¾?
    public function course()
    {
        return $this->difficulty->course;
    }
}