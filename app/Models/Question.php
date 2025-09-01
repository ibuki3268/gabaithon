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
     * ���̖�肪������v�iTile�j���擾���� (����1)
     */
    public function tile(): BelongsTo
    {
        return $this->belongsTo(Tile::class);
    }

    /**
     * ���̖�肪���I�����iChoices�j���擾���� (1�Α�)
     */
    public function choices(): HasMany
    {
        return $this->hasMany(Choice::class);
    }
}