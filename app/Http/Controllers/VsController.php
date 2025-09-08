<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class VsController extends Controller
{
    /**
     * 対戦画面（初期化 & 表示）
     */
    public function battle(Request $request)
    {
        if (!$request->session()->has('hands')) {
            // 各プレイヤーが持ち寄る牌（仮データ、34枚ずつ）
            $player1Tiles = [
                '一萬','二萬','三萬','四萬','五萬','六萬','七萬','八萬','九萬',
                '一筒','二筒','三筒','四筒','五筒','六筒','七筒','八筒','九筒',
                '一索','二索','三索','四索','五索','六索','七索','八索','九索',
                '東','南','西','中','白'
            ];

            $player2Tiles = $player1Tiles; // 同じく34枚
            $player3Tiles = $player1Tiles; // 同じく34枚

            // 全員分を合体 → 102枚
            $allTiles = array_merge($player1Tiles, $player2Tiles, $player3Tiles);
            shuffle($allTiles);

            // 手牌配布（13枚ずつ）
            $hands = [
                'player1' => array_slice($allTiles, 0, 13),
                'player2' => array_slice($allTiles, 13, 13),
                'player3' => array_slice($allTiles, 26, 13),
            ];

            // 捨て牌置き場
            $discards = [
                'player1' => [],
                'player2' => [],
                'player3' => [],
            ];

            // 残り牌（102 - 39 = 63 枚）
            $wall = array_slice($allTiles, 39);

            // ログで確認
            \Log::info('初期化: allTiles count', ['count' => count($allTiles)]);
            \Log::info('初期化: wall count', ['count' => count($wall)]);

            // セッション保存
            $request->session()->put('hands', $hands);
            $request->session()->put('discards', $discards);
            $request->session()->put('wall', $wall);
        }

        $hands = $request->session()->get('hands');
        $discards = $request->session()->get('discards');
        $wall = $request->session()->get('wall');

        return view('game.battle', compact('hands', 'discards', 'wall'));
    }

    /**
     * 山から引く
     */
    public function draw(Request $request, $player)
    {
        $wall = $request->session()->get('wall', []);
        $hands = $request->session()->get('hands', []);
        $discards = $request->session()->get('discards', []);

        if (!empty($wall)) {
            $tile = array_shift($wall);
            $hands[$player][] = $tile;

            \Log::info("{$player} がツモ", ['tile' => $tile, 'wall残り' => count($wall)]);
        } else {
            \Log::warning("{$player} が引こうとしたが山が空");
        }

        // セッション更新
        $request->session()->put('wall', $wall);
        $request->session()->put('hands', $hands);
        $request->session()->put('discards', $discards);

        return redirect()->route('vs.battle');
    }


    public function reset(Request $request)
{
    $request->session()->forget(['hands', 'discards', 'wall']);
    \Log::info('セッションリセット');
    return redirect()->route('vs.battle');
}

}
