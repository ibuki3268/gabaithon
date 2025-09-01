<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Choice extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'question_id',
        'text',
        'is_correct',
    ];

    /**
     * ���̑I��������������iQuestion�j���擾���� (����1)
     */
    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }
}
