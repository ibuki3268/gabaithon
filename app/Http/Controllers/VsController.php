<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\MahjongWinChecker;
use App\Events\GameStateUpdate; // 追加

class VsController extends Controller
{
    private $winChecker;

    public function __construct()
    {
        $this->winChecker = new MahjongWinChecker();
    }

    // カスタム牌山の定義（各プレイヤーが34種類の牌を持参）
    private function getCustomTileSet($playerId)
    {
        // 基本牌セット（34種類）
        $baseTiles = [
            // 萬子
            '一萬', '二萬', '三萬', '四萬', '五萬', '六萬', '七萬', '八萬', '九萬',
            // 筒子
            '一筒', '二筒', '三筒', '四筒', '五筒', '六筒', '七筒', '八筒', '九筒',
            // 索子
            '一索', '二索', '三索', '四索', '五索', '六索', '七索', '八索', '九索',
            // 字牌
            '東', '南', '西', '北', '中', '白', '發'
        ];

        // プレイヤーごとのカスタマイズ例
        switch ($playerId) {
            case 'player1':
                // プレイヤー1: バランス型
                return $baseTiles;
            case 'player2':
                // プレイヤー2: 字牌重視型
                $customTiles = array_slice($baseTiles, 0, 27); // 数牌のみ27種類
                $customTiles = array_merge($customTiles, array_fill(0, 7, '中')); // 中を7枚追加
                return $customTiles;
            case 'player3':
                // プレイヤー3: 筒子重視型
                $customTiles = array_merge(
                    array_slice($baseTiles, 0, 9),  // 萬子
                    array_fill(0, 9, '五筒'),       // 筒子の五筒を9枚
                    array_slice($baseTiles, 18, 9), // 索子
                    array_slice($baseTiles, 27, 7)  // 字牌
                );
                return $customTiles;
            default:
                return $baseTiles;
        }
    }

    /**
     * 対戦画面（初期化 & 表示）
     */
    public function battle(Request $request)
    {
        // 初期化はセッションにまだ 'hands' がないときだけ
        if (!$request->session()->has('hands')) {
            // 各プレイヤーのカスタム牌山を作成
            $player1Tiles = $this->getCustomTileSet('player1');
            $player2Tiles = $this->getCustomTileSet('player2');
            $player3Tiles = $this->getCustomTileSet('player3');

            // 全ての牌を混合
            $allTiles = array_merge($player1Tiles, $player2Tiles, $player3Tiles);
            shuffle($allTiles);

            // 手牌配布（各13枚）
            $hands = [
                'player1' => array_slice($allTiles, 0, 13),
                'player2' => array_slice($allTiles, 13, 13),
                'player3' => array_slice($allTiles, 26, 13),
            ];

            // 手牌をソート
            foreach ($hands as $player => &$hand) {
                $hand = $this->sortTiles($hand);
            }

            $discards = [
                'player1' => [],
                'player2' => [],
                'player3' => [],
            ];

            $wall = array_slice($allTiles, 39);

            $request->session()->put('hands', $hands);
            $request->session()->put('discards', $discards);
            $request->session()->put('wall', $wall);
            $request->session()->put('currentPlayer', 'player1');
            $request->session()->put('turnCount', 0);

            Log::info('新しいゲームを開始', [
                'totalTiles' => count($allTiles),
                'wallTiles' => count($wall)
            ]);

            // ゲーム開始をブロードキャスト
            $this->broadcastGameState($request, 'gameStart', [
                'message' => '新しいゲームが開始されました'
            ]);
        }

        $hands = $request->session()->get('hands');
        $discards = $request->session()->get('discards');
        $wall = $request->session()->get('wall');
        $currentPlayer = $request->session()->get('currentPlayer');
        $turnCount = $request->session()->get('turnCount', 0);

        return view('game.battle', compact('hands', 'discards', 'wall', 'currentPlayer', 'turnCount'));
    }

    /**
     * 牌をソートする
     */
    private function sortTiles($tiles)
    {
        $order = [
            // 萬子
            '一萬' => 1, '二萬' => 2, '三萬' => 3, '四萬' => 4, '五萬' => 5,
            '六萬' => 6, '七萬' => 7, '八萬' => 8, '九萬' => 9,
            // 筒子
            '一筒' => 11, '二筒' => 12, '三筒' => 13, '四筒' => 14, '五筒' => 15,
            '六筒' => 16, '七筒' => 17, '八筒' => 18, '九筒' => 19,
            // 索子
            '一索' => 21, '二索' => 22, '三索' => 23, '四索' => 24, '五索' => 25,
            '六索' => 26, '七索' => 27, '八索' => 28, '九索' => 29,
            // 字牌
            '東' => 31, '南' => 32, '西' => 33, '北' => 34,
            '中' => 35, '白' => 36, '發' => 37
        ];

        usort($tiles, function($a, $b) use ($order) {
            return ($order[$a] ?? 999) <=> ($order[$b] ?? 999);
        });

        return $tiles;
    }

    /**
     * ツモして捨てる（手番制）
     */
    public function drawAndDiscard(Request $request, $tileIndex)
    {
        $hands = $request->session()->get('hands', []);
        $discards = $request->session()->get('discards', []);
        $wall = $request->session()->get('wall', []);
        $currentPlayer = $request->session()->get('currentPlayer', 'player1');
        $turnCount = $request->session()->get('turnCount', 0);

        $drawnTile = null;

        // 山から1枚引く
        if (!empty($wall)) {
            $drawnTile = array_shift($wall);
            $hands[$currentPlayer][] = $drawnTile;
            Log::info("{$currentPlayer} がツモ", [
                'tile' => $drawnTile, 
                'wall残り' => count($wall),
                'turn' => $turnCount
            ]);
        } else {
            Log::warning("{$currentPlayer} が引こうとしたが山が空");
            
            // 流局をブロードキャスト
            $this->broadcastGameState($request, 'draw', [
                'message' => '牌山が空になりました。流局です。'
            ]);
            
            return redirect()->route('vs.battle')->with('message', '牌山が空になりました。流局です。');
        }

        // 手牌をソート
        $hands[$currentPlayer] = $this->sortTiles($hands[$currentPlayer]);

        // ツモ上がりの判定（捨てる前）
        if (count($hands[$currentPlayer]) == 14) {
            $winResult = $this->winChecker->checkWin($hands[$currentPlayer]);
            if ($winResult && $winResult['win']) {
                // 役判定
                $yakuResult = $this->winChecker->checkYaku(
                    array_slice($hands[$currentPlayer], 0, 13), // 手牌13枚
                    $drawnTile, // ツモった牌
                    true, // ツモ
                    $request->session()->get("riichi_{$currentPlayer}", false) // リーチ状態
                );

                Log::info("{$currentPlayer} がツモで上がり！", [
                    'winType' => $winResult['type'],
                    'han' => $winResult['han'],
                    'yaku' => $yakuResult['yaku'],
                    'score' => $yakuResult['score']
                ]);

                $request->session()->put('gameResult', [
                    'winner' => $currentPlayer,
                    'winType' => 'ツモ',
                    'winCondition' => $winResult,
                    'yaku' => $yakuResult,
                    'finalHand' => $hands[$currentPlayer]
                ]);

                // ツモ上がりをブロードキャスト
                $this->broadcastGameState($request, 'win', [
                    'winner' => $currentPlayer,
                    'winType' => 'ツモ',
                    'winCondition' => $winResult,
                    'yaku' => $yakuResult,
                    'finalHand' => $hands[$currentPlayer],
                    'winningTile' => $drawnTile
                ]);

                return redirect()->route('vs.battle');
            }
        }

        // 捨てる
        if (isset($hands[$currentPlayer][$tileIndex]) && count($hands[$currentPlayer]) == 14) {
            $discardTile = $hands[$currentPlayer][$tileIndex];
            unset($hands[$currentPlayer][$tileIndex]);
            $hands[$currentPlayer] = array_values($hands[$currentPlayer]);
            $discards[$currentPlayer][] = $discardTile;
            Log::info("{$currentPlayer} が捨てた", [
                'tile' => $discardTile,
                'turn' => $turnCount
            ]);
        } else {
            Log::warning("{$currentPlayer} が不正な牌を捨てようとした", [
                'tileIndex' => $tileIndex,
                'handSize' => count($hands[$currentPlayer])
            ]);
            return redirect()->route('vs.battle')->with('error', '不正な操作です。');
        }

        // セッション更新
        $request->session()->put('hands', $hands);
        $request->session()->put('discards', $discards);
        $request->session()->put('wall', $wall);
        $request->session()->put('turnCount', $turnCount + 1);

        // 次の手番に更新
        $players = ['player1', 'player2', 'player3'];
        $currentIndex = array_search($currentPlayer, $players);
        $nextIndex = ($currentIndex + 1) % count($players);
        $nextPlayer = $players[$nextIndex];
        $request->session()->put('currentPlayer', $nextPlayer);

        // ツモと捨て牌をブロードキャスト
        $this->broadcastGameState($request, 'turn', [
            'action' => 'drawAndDiscard',
            'player' => $currentPlayer,
            'drawnTile' => $drawnTile,
            'discardedTile' => $discardTile,
            'nextPlayer' => $nextPlayer,
            'turnCount' => $turnCount + 1
        ]);

        return redirect()->route('vs.battle');
    }

    /**
     * ロン判定（他家の捨て牌で上がり）
     */
    public function checkRon(Request $request, $discardingPlayer, $tileIndex)
    {
        $hands = $request->session()->get('hands', []);
        $discards = $request->session()->get('discards', []);
        $currentPlayer = $request->session()->get('currentPlayer');

        // 捨てられた牌を取得
        if (!isset($discards[$discardingPlayer][$tileIndex])) {
            return redirect()->route('vs.battle')->with('error', '不正な操作です。');
        }

        $discardedTile = $discards[$discardingPlayer][$tileIndex];

        // 各プレイヤーの上がり判定
        $ronPlayers = [];
        foreach ($hands as $player => $hand) {
            if ($player === $discardingPlayer) continue; // 捨て牌のプレイヤーは除外

            $winResult = $this->winChecker->checkWin($hand, $discardedTile);
            if ($winResult && $winResult['win']) {
                $yakuResult = $this->winChecker->checkYaku(
                    $hand, 
                    $discardedTile, 
                    false, // ロン
                    $request->session()->get("riichi_{$player}", false)
                );

                $ronPlayers[] = [
                    'player' => $player,
                    'winCondition' => $winResult,
                    'yaku' => $yakuResult
                ];
            }
        }

        if (!empty($ronPlayers)) {
            // 複数のロンがある場合は上家優先
            $winner = $ronPlayers[0];

            Log::info("{$winner['player']} がロンで上がり！", [
                'discardingPlayer' => $discardingPlayer,
                'winningTile' => $discardedTile,
                'winType' => $winner['winCondition']['type'],
                'yaku' => $winner['yaku']['yaku'],
                'score' => $winner['yaku']['score']
            ]);

            $request->session()->put('gameResult', [
                'winner' => $winner['player'],
                'winType' => 'ロン',
                'winCondition' => $winner['winCondition'],
                'yaku' => $winner['yaku'],
                'finalHand' => array_merge($hands[$winner['player']], [$discardedTile]),
                'discardingPlayer' => $discardingPlayer
            ]);

            // ロン上がりをブロードキャスト
            $this->broadcastGameState($request, 'win', [
                'winner' => $winner['player'],
                'winType' => 'ロン',
                'winCondition' => $winner['winCondition'],
                'yaku' => $winner['yaku'],
                'finalHand' => array_merge($hands[$winner['player']], [$discardedTile]),
                'discardingPlayer' => $discardingPlayer,
                'winningTile' => $discardedTile
            ]);

            return redirect()->route('vs.battle');
        }

        // ロンできない場合もブロードキャスト
        $this->broadcastGameState($request, 'ronCheck', [
            'message' => 'ロンできるプレイヤーがいません',
            'discardingPlayer' => $discardingPlayer,
            'discardedTile' => $discardedTile
        ]);

        return redirect()->route('vs.battle')->with('message', 'ロンできるプレイヤーがいません。');
    }

    /**
     * リーチ宣言
     */
    public function declareRiichi(Request $request, $player)
    {
        $currentPlayer = $request->session()->get('currentPlayer');
        
        if ($player !== $currentPlayer) {
            return redirect()->route('vs.battle')->with('error', '手番ではありません。');
        }

        // リーチ状態をセット
        $request->session()->put("riichi_{$player}", true);
        
        Log::info("{$player} がリーチを宣言");
        
        // リーチ宣言をブロードキャスト
        $this->broadcastGameState($request, 'riichi', [
            'player' => $player,
            'action' => 'riichi_declared',
            'message' => "{$player} がリーチを宣言しました！"
        ]);
        
        return redirect()->route('vs.battle')->with('message', "{$player} がリーチを宣言しました！");
    }

    /**
     * AIプレイヤーの自動行動
     */
    private function aiPlay(Request $request, $player)
    {
        $hands = $request->session()->get('hands', []);
        $wall = $request->session()->get('wall', []);

        if (empty($wall)) return;

        // AIの簡単なロジック：ランダムに捨てる
        $handSize = count($hands[$player]);
        if ($handSize > 0) {
            $randomIndex = rand(0, $handSize - 1);
            $this->drawAndDiscard($request, $randomIndex);
        }
    }

    /**
     * リセット処理
     */
    public function reset(Request $request)
    {
        $request->session()->forget(['hands', 'discards', 'wall', 'currentPlayer', 'turnCount', 'gameResult']);
        
        // リーチ状態もクリア
        $players = ['player1', 'player2', 'player3'];
        foreach ($players as $player) {
            $request->session()->forget("riichi_{$player}");
        }
        
        Log::info('セッションリセット');
        
        // リセットをブロードキャスト
        $this->broadcastGameState($request, 'reset', [
            'message' => 'ゲームがリセットされました'
        ]);
        
        return redirect()->route('vs.battle');
    }

    /**
     * ゲーム統計表示
     */
    public function stats(Request $request)
    {
        $hands = $request->session()->get('hands', []);
        $discards = $request->session()->get('discards', []);
        $wall = $request->session()->get('wall', []);
        $turnCount = $request->session()->get('turnCount', 0);

        $stats = [
            'turnCount' => $turnCount,
            'wallRemaining' => count($wall),
            'totalDiscarded' => array_sum(array_map('count', $discards)),
            'playerHandSizes' => array_map('count', $hands)
        ];

        return response()->json($stats);
    }

    /**
     * ゲーム状態をブロードキャストする共通メソッド
     */
    private function broadcastGameState(Request $request, $eventType, $additionalData = [])
    {
        $hands = $request->session()->get('hands', []);
        $discards = $request->session()->get('discards', []);
        $wall = $request->session()->get('wall', []);
        $currentPlayer = $request->session()->get('currentPlayer');
        $turnCount = $request->session()->get('turnCount', 0);

        // セキュリティのため、他プレイヤーの手牌は枚数のみ送信
        $publicHands = [];
        foreach ($hands as $player => $hand) {
            $publicHands[$player] = [
                'count' => count($hand),
                'tiles' => $player === 'player1' ? $hand : [] // player1の手牌のみ表示（デモ用）
            ];
        }

        $gameData = array_merge([
            'eventType' => $eventType,
            'hands' => $publicHands,
            'discards' => $discards,
            'wallCount' => count($wall),
            'currentPlayer' => $currentPlayer,
            'turnCount' => $turnCount,
            'riichiStatus' => [
                'player1' => $request->session()->get('riichi_player1', false),
                'player2' => $request->session()->get('riichi_player2', false),
                'player3' => $request->session()->get('riichi_player3', false),
            ],
            'gameResult' => $request->session()->get('gameResult', null)
        ], $additionalData);

        // セッションIDをゲームIDとして使用
        $gameId = $request->session()->getId();

        // イベントをブロードキャスト
        broadcast(new GameStateUpdate($gameData, $gameId));
        
        Log::info('ゲーム状態をブロードキャスト', [
            'eventType' => $eventType,
            'gameId' => $gameId,
            'currentPlayer' => $currentPlayer,
            'turnCount' => $turnCount
        ]);
    }

    /**
     * プレイヤー参加処理（将来の拡張用）
     */
    public function joinGame(Request $request, $playerId)
    {
        // プレイヤーの参加をブロードキャスト
        $this->broadcastGameState($request, 'playerJoin', [
            'joinedPlayer' => $playerId,
            'message' => "{$playerId} がゲームに参加しました"
        ]);

        return response()->json(['status' => 'success', 'player' => $playerId]);
    }

    /**
     * プレイヤー退出処理（将来の拡張用）
     */
    public function leaveGame(Request $request, $playerId)
    {
        // プレイヤーの退出をブロードキャスト
        $this->broadcastGameState($request, 'playerLeave', [
            'leftPlayer' => $playerId,
            'message' => "{$playerId} がゲームを退出しました"
        ]);

        return response()->json(['status' => 'success', 'player' => $playerId]);
    }

    /**
     * ゲーム状態取得（AJAX用）
     */
    public function getGameState(Request $request)
    {
        $hands = $request->session()->get('hands', []);
        $discards = $request->session()->get('discards', []);
        $wall = $request->session()->get('wall', []);
        $currentPlayer = $request->session()->get('currentPlayer');
        $turnCount = $request->session()->get('turnCount', 0);

        return response()->json([
            'hands' => $hands,
            'discards' => $discards,
            'wallCount' => count($wall),
            'currentPlayer' => $currentPlayer,
            'turnCount' => $turnCount,
            'riichiStatus' => [
                'player1' => $request->session()->get('riichi_player1', false),
                'player2' => $request->session()->get('riichi_player2', false),
                'player3' => $request->session()->get('riichi_player3', false),
            ],
            'gameResult' => $request->session()->get('gameResult', null)
        ]);
    }
}