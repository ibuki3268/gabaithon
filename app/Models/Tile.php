<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany; // ���̍s��ǉ�

class Tile extends Model
{
    use HasFactory;

    /**
     * ���̔v�������iQuestions�j���擾���� (1�Α�)
     */
    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }

    protected $fillable = [
        'name',
        'type',
        'num',
        'image_path',
    ];
}
