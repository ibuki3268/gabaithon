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
        // テーブルが空だったので仮データ置いてます
        $discardedTiles = [
            [
                'id' => 1,
                'course_id' => 'あ',
                'tile_id' => 'あ',
                'created_at' => 'あ',
                'updated_at' => 'あ'
            ],
            
        ];

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