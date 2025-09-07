<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Question extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'questions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    // Seeder�ł̗��p�ɍ��킹�āA�ꊇ����\�ȑ������X�V
    protected $fillable = [
        'day',
        'course_id',
        'difficulty_id',
        'question',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'question' => 'array', // 'question'�J������JSON����z��֎����ϊ�
    ];

    /**
     * ���̖�肪������v�iTile�j���擾���� (����1)
     * ����: ���̃����[�V�������@�\������ɂ� 'questions' �e�[�u���� 'tile_id' �J�������K�v�ł��B
     */
    public function tile(): BelongsTo
    {
        return $this->belongsTo(Tile::class);
    }

    /**
     * ��̓�Փx�ɑ�����
     */
    public function difficulty(): BelongsTo
    {
        return $this->belongsTo(Difficulty::class);
    }

    /**
     * �֘A�I�ɃR�[�X���擾
     */
    public function course()
    {
        // difficulty�����[�V�������o�R����Course���f�����擾���܂�
        return $this->difficulty->course;
    }
}
