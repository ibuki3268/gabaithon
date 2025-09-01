<?php

namespace App\Http\Controllers;

use App\Models\Tile; // Tile���f�����g�����߂̐錾
use Illuminate\Http\Request;

class QuizController extends Controller
{
    /**
     * �w�肳�ꂽ�v�Ɋ֘A����N�C�Y��\������
     */
    public function show(Tile $tile)
    {
        // �v�i$tile�j�ɕR�Â����iquestions�j�ƁA
        // ����Ɋe���ɕR�Â��I�����ichoices�j����x�ɓǂݍ���
        $questions = $tile->questions()->with('choices')->get();

        // �v�itile�j�Ɩ�胊�X�g�iquestions�j��'quiz'�Ƃ����r���[�ɓn��
        return view('quiz', [
            'tile' => $tile,
            'questions' => $questions,
        ]);
    }
}

