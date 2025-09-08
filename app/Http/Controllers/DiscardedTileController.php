<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DiscardedTileController extends Controller
{
    /**
     * 捨て牌一覧画面を表示
     */
    public function index()
    {
        // ジャンル・コースで絞り込み
        $genre = request('genre');
        $course = request('course');
        $query = \DB::table('sutehai');
        if ($genre) {
            $query->where('genre', $genre);
        }
        if ($course) {
            $query->where('question_id', $course);
        }
        $discardedTiles = $query->get();
        return view('discarded-tiles', compact('discardedTiles'));
    }

    /**
     * 捨て牌から再挑戦
     */
    public function retry($id)
    {
        // 実際の実装ではsutehaiテーブルから該当データを取得
        // 現在は仮の処理
        
        // クイズ画面にリダイレクト（既存のクイズ機能を利用）
        return redirect()->route('quiz.show', ['tile' => $id])
            ->with('message', '捨て牌から再挑戦を開始します！');
    }
}