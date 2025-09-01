<?php

namespace App\Http\Controllers;

use App\Models\Tile; // Tileモデルを使うための宣言
use Illuminate\Http\Request;

class QuizController extends Controller
{
    /**
     * 指定された牌に関連するクイズを表示する
     */
    public function show(Tile $tile)
    {
        // 牌（$tile）に紐づく問題（questions）と、
        // さらに各問題に紐づく選択肢（choices）を一度に読み込む
        $questions = $tile->questions()->with('choices')->get();

        // 牌（tile）と問題リスト（questions）を'quiz'というビューに渡す
        return view('quiz', [
            'tile' => $tile,
            'questions' => $questions,
        ]);
    }
}

