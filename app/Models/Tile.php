<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany; // ‚±‚Ìs‚ð’Ç‰Á

class Tile extends Model
{
    use HasFactory;

    /**
     * ‚±‚Ì”v‚ªŽ‚Â–â‘èiQuestionsj‚ðŽæ“¾‚·‚é (1‘Î‘½)
     */
    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }
}
