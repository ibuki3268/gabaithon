@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-3xl font-bold mb-6 text-center">麻雀対戦</h1>
    
    <!-- ゲーム情報表示エリア -->
    <div class="game-info bg-gray-100 dark:bg-gray-800 p-4 rounded-lg mb-6 shadow-md">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div id="current-player" class="text-lg font-semibold text-blue-600 dark:text-blue-400">
                現在の手番: {{ $currentPlayer }}
            </div>
            <div id="turn-count" class="text-md text-gray-700 dark:text-gray-300">
                ターン: {{ $turnCount }}
            </div>
            <div id="wall-count" class="text-md text-gray-700 dark:text-gray-300">
                山: {{ count($wall) }}枚
            </div>
        </div>
    </div>

    <!-- ゲーム結果表示 -->
    @if(session('gameResult'))
        <div class="game-result bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 shadow">
            <div class="font-bold text-xl mb-2">🎉 ゲーム終了！</div>
            <div class="text-lg">
                <strong>{{ session('gameResult.winner') }}</strong> が 
                <strong>{{ session('gameResult.winType') }}</strong> で上がりました！
            </div>
            @if(isset(session('gameResult.yaku')['yaku']))
                <div class="mt-2 text-sm">
                    <strong>役:</strong> {{ implode(', ', session('gameResult.yaku.yaku')) }}
                </div>
            @endif
        </div>
    @endif

    <!-- メッセージ表示 -->
    @if(session('message'))
        <div class="alert bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded mb-4 shadow">
            {{ session('message') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 shadow">
            {{ session('error') }}
        </div>
    @endif

    <!-- プレイヤー1エリア（自分） -->
    <div class="player-area mb-8 bg-blue-50 dark:bg-blue-900 p-4 rounded-lg shadow" id="player1-area">
        <div id="player1-status" class="player-status mb-4 {{ $currentPlayer === 'player1' ? 'ring-2 ring-blue-500' : '' }}">
            <h2 class="text-xl font-bold text-blue-800 dark:text-blue-200">
                プレイヤー1 (あなた)
                @if($currentPlayer === 'player1')
                    <span class="text-green-600 ml-2">← 手番</span>
                @endif
            </h2>
            @if(session('riichi_player1'))
                <span class="riichi-indicator text-orange-500 font-bold text-lg">🔥 リーチ</span>
            @endif
        </div>
        
        <!-- 手牌表示 -->
        <div id="player1-hand" class="hand-area mb-4">
            <div class="hand-count text-sm text-gray-600 dark:text-gray-400 mb-2">
                手牌: {{ count($hands['player1']) }}枚
            </div>
            <div class="tiles flex flex-wrap gap-2">
                @foreach($hands['player1'] as $index => $tile)
                    @if($currentPlayer === 'player1' && count($hands['player1']) === 14)
                        <button class="tile game-action-btn bg-white border-2 border-gray-300 px-3 py-2 rounded-lg hover:bg-gray-100 hover:border-blue-400 transform hover:scale-105 transition-all duration-200 shadow-sm" 
                                onclick="discardTile({{ $index }})"
                                title="この牌を捨てる">
                            {{ $tile }}
                        </button>
                    @else
                        <div class="tile bg-white border border-gray-300 px-3 py-2 rounded-lg shadow-sm">
                            {{ $tile }}
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
        
        <!-- 捨て牌表示 -->
        <div id="player1-discards" class="discard-area">
            <div class="text-sm text-gray-600 dark:text-gray-400 mb-2">捨て牌:</div>
            <div class="flex flex-wrap gap-1">
                @foreach($discards['player1'] as $index => $tile)
                    <span class="discarded-tile bg-gray-200 dark:bg-gray-700 px-2 py-1 rounded text-sm cursor-pointer hover:bg-gray-300 dark:hover:bg-gray-600"
                          onclick="checkRonOnTile('player1', {{ $index }})"
                          title="この牌でロンする">
                        {{ $tile }}
                    </span>
                @endforeach
            </div>
        </div>
    </div>

    <!-- プレイヤー2エリア -->
    <div class="player-area mb-8 bg-red-50 dark:bg-red-900 p-4 rounded-lg shadow" id="player2-area">
        <div id="player2-status" class="player-status mb-4 {{ $currentPlayer === 'player2' ? 'ring-2 ring-red-500' : '' }}">
            <h2 class="text-xl font-bold text-red-800 dark:text-red-200">
                プレイヤー2
                @if($currentPlayer === 'player2')
                    <span class="text-green-600 ml-2">← 手番</span>
                @endif
            </h2>
            @if(session('riichi_player2'))
                <span class="riichi-indicator text-orange-500 font-bold text-lg">🔥 リーチ</span>
            @endif
        </div>
        
        <div id="player2-hand" class="hand-area mb-4">
            <div class="hand-count text-sm text-gray-600 dark:text-gray-400 mb-2">
                手牌: {{ count($hands['player2']) }}枚
            </div>
            <div class="tiles flex flex-wrap gap-2">
                @for($i = 0; $i < count($hands['player2']); $i++)
                    <div class="tile-back bg-gray-500 px-3 py-2 rounded-lg text-white shadow-sm">🀫</div>
                @endfor
            </div>
        </div>
        
        <div id="player2-discards" class="discard-area">
            <div class="text-sm text-gray-600 dark:text-gray-400 mb-2">捨て牌:</div>
            <div class="flex flex-wrap gap-1">
                @foreach($discards['player2'] as $index => $tile)
                    <span class="discarded-tile bg-gray-200 dark:bg-gray-700 px-2 py-1 rounded text-sm cursor-pointer hover:bg-gray-300 dark:hover:bg-gray-600"
                          onclick="checkRonOnTile('player2', {{ $index }})"
                          title="この牌でロンする">
                        {{ $tile }}
                    </span>
                @endforeach
            </div>
        </div>
    </div>

    <!-- プレイヤー3エリア -->
    <div class="player-area mb-8 bg-green-50 dark:bg-green-900 p-4 rounded-lg shadow" id="player3-area">
        <div id="player3-status" class="player-status mb-4 {{ $currentPlayer === 'player3' ? 'ring-2 ring-green-500' : '' }}">
            <h2 class="text-xl font-bold text-green-800 dark:text-green-200">
                プレイヤー3
                @if($currentPlayer === 'player3')
                    <span class="text-green-600 ml-2">← 手番</span>
                @endif
            </h2>
            @if(session('riichi_player3'))
                <span class="riichi-indicator text-orange-500 font-bold text-lg">🔥 リーチ</span>
            @endif
        </div>
        
        <div id="player3-hand" class="hand-area mb-4">
            <div class="hand-count text-sm text-gray-600 dark:text-gray-400 mb-2">
                手牌: {{ count($hands['player3']) }}枚
            </div>
            <div class="tiles flex flex-wrap gap-2">
                @for($i = 0; $i < count($hands['player3']); $i++)
                    <div class="tile-back bg-gray-500 px-3 py-2 rounded-lg text-white shadow-sm">🀫</div>
                @endfor
            </div>
        </div>
        
        <div id="player3-discards" class="discard-area">
            <div class="text-sm text-gray-600 dark:text-gray-400 mb-2">捨て牌:</div>
            <div class="flex flex-wrap gap-1">
                @foreach($discards['player3'] as $index => $tile)
                    <span class="discarded-tile bg-gray-200 dark:bg-gray-700 px-2 py-1 rounded text-sm cursor-pointer hover:bg-gray-300 dark:hover:bg-gray-600"
                          onclick="checkRonOnTile('player3', {{ $index }})"
                          title="この牌でロンする">
                        {{ $tile }}
                    </span>
                @endforeach
            </div>
        </div>
    </div>

    <!-- ゲームアクションボタン -->
    <div class="action-buttons flex flex-wrap gap-4 mt-6 justify-center">
        @if($currentPlayer === 'player1' && count($hands['player1']) === 13)
            <button class="game-action-btn bg-orange-500 text-white px-6 py-3 rounded-lg hover:bg-orange-600 transform hover:scale-105 transition-all duration-200 shadow-lg font-bold" 
                    onclick="declareRiichi()"
                    title="リーチを宣言する">
                🔥 リーチ
            </button>
        @endif
        
        <button class="game-action-btn bg-gray-500 text-white px-6 py-3 rounded-lg hover:bg-gray-600 transform hover:scale-105 transition-all duration-200 shadow-lg font-bold" 
                onclick="resetGame()"
                title="ゲームをリセットする">
            🔄 リセット
        </button>
        
        <button class="game-action-btn bg-blue-500 text-white px-6 py-3 rounded-lg hover:bg-blue-600 transform hover:scale-105 transition-all duration-200 shadow-lg font-bold" 
                onclick="getStats()"
                title="ゲーム統計を表示">
            📊 統計
        </button>
    </div>

    <!-- デバッグ情報（開発用） -->
    @if(config('app.debug'))
        <div class="debug-info mt-8 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
            <h3 class="font-bold text-yellow-800 mb-2">デバッグ情報</h3>
            <div class="text-sm text-yellow-700">
                <p>セッションID: {{ session()->getId() }}</p>
                <p>現在のプレイヤー: {{ $currentPlayer }}</p>
                <p>手牌合計: {{ array_sum(array_map('count', $hands)) }}枚</p>
                <p>山の残り: {{ count($wall) }}枚</p>
            </div>
        </div>
    @endif
</div>

<!-- 通知表示エリア -->
<div id="notification-area" class="fixed top-4 right-4 z-50"></div>

<!-- モーダル用 -->
<div id="modal-overlay" class="fixed inset-0 bg-black bg-opacity-50 hidden z-40" onclick="closeModal()"></div>
<div id="stats-modal" class="fixed top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-white dark:bg-gray-800 p-6 rounded-lg shadow-xl z-50 hidden">
    <h3 class="text-xl font-bold mb-4">ゲーム統計</h3>
    <div id="stats-content"></div>
    <button onclick="closeModal()" class="mt-4 bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">閉じる</button>
</div>

<!-- JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // セッションIDを取得（ゲームルーム識別用）
    const gameId = '{{ session()->getId() }}';
    
    console.log('麻雀ゲーム初期化開始 - ゲームID:', gameId);
    
    // リアルタイム通信のセットアップ
    if (window.Echo) {
        window.Echo.channel(`mahjong-game-${gameId}`)
            .listen('.game.state.update', (data) => {
                console.log('ゲーム状態更新受信:', data);
                handleGameStateUpdate(data.gameData);
            })
            .error((error) => {
                console.error('Echo接続エラー:', error);
            });
        
        console.log('Echoチャンネル接続完了');
    } else {
        console.error('Laravel Echoが利用できません。Pusher設定を確認してください。');
    }

    function handleGameStateUpdate(gameData) {
        const { eventType } = gameData;
        
        console.log('イベント処理:', eventType, gameData);
        
        switch (eventType) {
            case 'turn':
                handleTurnUpdate(gameData);
                break;
            case 'riichi':
                handleRiichiUpdate(gameData);
                break;
            case 'win':
                handleWinUpdate(gameData);
                break;
            case 'gameStart':
                handleGameStart(gameData);
                break;
            case 'reset':
                handleGameReset(gameData);
                break;
            case 'draw':
                handleGameDraw(gameData);
                break;
            default:
                console.log('未知のイベントタイプ:', eventType);
        }
        
        // 共通UI更新
        updateGameUI(gameData);
    }

    function handleTurnUpdate(gameData) {
        // 手番変更の表示更新
        updateCurrentPlayer(gameData.nextPlayer || gameData.currentPlayer);
        updateTurnCount(gameData.turnCount);
        
        // 捨て牌の通知
        if (gameData.discardedTile) {
            showNotification(`${gameData.player} が ${gameData.discardedTile} を捨てました`, 'discard');
            highlightLatestDiscard(gameData.player);
        }
        
        // ツモの通知
        if (gameData.drawnTile && gameData.player) {
            showNotification(`${gameData.player} がツモしました`, 'info');
        }
    }

    function handleRiichiUpdate(gameData) {
        showNotification(`${gameData.player} がリーチを宣言しました！`, 'riichi');
        updatePlayerRiichiStatus(gameData.player, true);
    }

    function handleWinUpdate(gameData) {
        showNotification(`🎉 ${gameData.winner} が ${gameData.winType} で上がりました！`, 'win');
        disableGameActions();
        
        // 3秒後にページをリロードして結果を表示
        setTimeout(() => {
            window.location.reload();
        }, 3000);
    }

    function handleGameStart(gameData) {
        showNotification('新しいゲームが開始されました', 'info');
    }

    function handleGameReset(gameData) {
        showNotification('ゲームがリセットされました', 'info');
        setTimeout(() => {
            window.location.reload();
        }, 1000);
    }

    function handleGameDraw(gameData) {
        showNotification('流局：山が空になりました', 'info');
        disableGameActions();
    }

    function updateGameUI(gameData) {
        // 山の残り枚数更新
        updateWallCount(gameData.wallCount);
        
        // 手牌の更新
        if (gameData.hands) {
            updatePlayerHands(gameData.hands);
        }
        
        // 捨て牌の更新
        if (gameData.discards) {
            updateDiscardPiles(gameData.discards);
        }
        
        // リーチ状態の更新
        if (gameData.riichiStatus) {
            updateAllRiichiStatus(gameData.riichiStatus);
        }
    }

    function updateCurrentPlayer(player) {
        const element = document.querySelector('#current-player');
        if (element) {
            element.textContent = `現在の手番: ${player}`;
        }
        
        // 手番のハイライト更新
        document.querySelectorAll('.player-status').forEach(status => {
            status.classList.remove('ring-2', 'ring-blue-500', 'ring-red-500', 'ring-green-500');
        });
        
        const currentPlayerStatus = document.querySelector(`#${player}-status`);
        if (currentPlayerStatus) {
            if (player === 'player1') {
                currentPlayerStatus.classList.add('ring-2', 'ring-blue-500');
            } else if (player === 'player2') {
                currentPlayerStatus.classList.add('ring-2', 'ring-red-500');
            } else if (player === 'player3') {
                currentPlayerStatus.classList.add('ring-2', 'ring-green-500');
            }
        }
    }

    function updateTurnCount(count) {
        const element = document.querySelector('#turn-count');
        if (element) {
            element.textContent = `ターン: ${count}`;
        }
    }

    function updateWallCount(count) {
        const element = document.querySelector('#wall-count');
        if (element) {
            element.textContent = `山: ${count}枚`;
        }
    }

    function updatePlayerHands(hands) {
        Object.keys(hands).forEach(player => {
            const handCountElement = document.querySelector(`#${player}-hand .hand-count`);
            if (handCountElement && hands[player]) {
                handCountElement.textContent = `手牌: ${hands[player].count}枚`;
            }
        });
    }

    function updateDiscardPiles(discards) {
        Object.keys(discards).forEach(player => {
            const discardContainer = document.querySelector(`#${player}-discards .flex`);
            if (discardContainer && discards[player]) {
                // 捨て牌エリアを更新
                discardContainer.innerHTML = '';
                discards[player].forEach((tile, index) => {
                    const tileElement = document.createElement('span');
                    tileElement.className = 'discarded-tile bg-gray-200 dark:bg-gray-700 px-2 py-1 rounded text-sm cursor-pointer hover:bg-gray-300 dark:hover:bg-gray-600';
                    tileElement.textContent = tile;
                    tileElement.onclick = () => checkRonOnTile(player, index);
                    tileElement.title = 'この牌でロンする';
                    
                    // 最新の捨て牌にハイライト効果
                    if (index === discards[player].length - 1) {
                        tileElement.classList.add('latest-discard');
                    }
                    
                    discardContainer.appendChild(tileElement);
                });
            }
        });
    }

    function updatePlayerRiichiStatus(player, isRiichi) {
        const statusElement = document.querySelector(`#${player}-status`);
        const indicatorExists = statusElement.querySelector('.riichi-indicator');
        
        if (isRiichi && !indicatorExists) {
            statusElement.classList.add('riichi-status');
            const indicator = document.createElement('span');
            indicator.className = 'riichi-indicator text-orange-500 font-bold text-lg ml-4';
            indicator.textContent = '🔥 リーチ';
            statusElement.appendChild(indicator);
        }
    }

    function updateAllRiichiStatus(riichiStatus) {
        Object.keys(riichiStatus).forEach(player => {
            updatePlayerRiichiStatus(player, riichiStatus[player]);
        });
    }

    function highlightLatestDiscard(player) {
        const discardElements = document.querySelectorAll(`#${player}-discards .discarded-tile`);
        const latestDiscard = discardElements[discardElements.length - 1];
        if (latestDiscard) {
            latestDiscard.classList.add('latest-discard');
            setTimeout(() => {
                latestDiscard.classList.remove('latest-discard');
            }, 2000);
        }
    }

    function showNotification(message, type = 'info') {
        const notificationArea = document.getElementById('notification-area');
        const notification = document.createElement('div');
        
        let bgColor = 'bg-gray-500';
        let duration = 3000;
        
        switch (type) {
            case 'discard':
                bgColor = 'bg-blue-500';
                break;
            case 'riichi':
                bgColor = 'bg-orange-500';
                duration = 4000;
                break;
            case 'win':
                bgColor = 'bg-green-500';
                duration = 5000;
                break;
            case 'info':
                bgColor = 'bg-blue-400';
                break;
        }
        
        notification.className = `notification ${bgColor} text-white px-4 py-2 rounded-lg font-bold mb-2 shadow-lg transform transition-all duration-300 translate-x-full`;
        notification.textContent = message;
        
        notificationArea.appendChild(notification);
        
        // アニメーション
        setTimeout(() => {
            notification.classList.remove('translate-x-full');
        }, 10);
        
        // 自動削除
        setTimeout(() => {
            notification.classList.add('translate-x-full');
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.remove();
                }
            }, 300);
        }, duration);
    }

    function disableGameActions() {
        const actionButtons = document.querySelectorAll('.game-action-btn');
        actionButtons.forEach(btn => {
            btn.disabled = true;
            btn.classList.add('opacity-50', 'cursor-not-allowed');
            btn.classList.remove('hover:scale-105');
        });
    }
});

// ゲーム操作関数
function discardTile(tileIndex) {
    console.log('牌を捨てる:', tileIndex);
    window.location.href = `/vs/draw-discard/${tileIndex}`;
}

function declareRiichi() {
    console.log('リーチ宣言');
    if (confirm('リーチを宣言しますか？')) {
        window.location.href = `/vs/riichi/player1`;
    }
}

function checkRonOnTile(discardingPlayer, tileIndex) {
    console.log('ロン判定:', discardingPlayer, tileIndex);
    if (confirm(`${discardingPlayer}の捨て牌でロンしますか？`)) {
        window.location.href = `/vs/ron/${discardingPlayer}/${tileIndex}`;
    }
}

function resetGame() {
    console.log('ゲームリセット');
    if (confirm('ゲームをリセットしますか？')) {
        window.location.href = `/vs/reset`;
    }
}

function getStats() {
    console.log('統計取得');
    fetch('/vs/stats')
        .then(response => response.json())
        .then(data => {
            showStatsModal(data);
        })
        .catch(error => {
            console.error('統計取得エラー:', error);
            alert('統計の取得に失敗しました');
        });
}

function showStatsModal(stats) {
    const content = document.getElementById('stats-content');
    content.innerHTML = `
        <div class="space-y-2">
            <div><strong>ターン数:</strong> ${stats.turnCount}</div>
            <div><strong>山の残り:</strong> ${stats.wallRemaining}枚</div>
            <div><strong>捨て牌総数:</strong> ${stats.totalDiscarded}枚</div>
            <div><strong>各プレイヤーの手牌:</strong></div>
            <ul class="ml-4">
                <li>プレイヤー1: ${stats.playerHandSizes.player1}枚</li>
                <li>プレイヤー2: ${stats.playerHandSizes.player2}枚</li>
                <li>プレイヤー3: ${stats.playerHandSizes.player3}枚</li>
            </ul>
        </div>
    `;
    
    document.getElementById('modal-overlay').classList.remove('hidden');
    document.getElementById('stats-modal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('modal-overlay').classList.add('hidden');
    document.getElementById('stats-modal').classList.add('hidden');
}
</script>

<style>
.riichi-status {
    @apply border-2 border-orange-400 bg-orange-50 dark:bg-orange-900;
}

.latest-discard {
    @apply bg-yellow-300 dark:bg-yellow-600;
    animation: highlight 2s ease-out;
}

@keyframes highlight {
    0% { 
        background-color: #fbbf24; 
        transform: scale(1.1);
    }
    50% { 
        background-color: #f59e0b; 
    }
    100% { 
        background-color: #e5e7eb; 
        transform: scale(1);
    }
}

.notification {
    animation: slideIn 0.3s ease-out;
}

@keyframes slideIn {
    from { transform: translateX(100%); }
    to { transform: translateX(0); }
}

.tile:hover {
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}

.game-action-btn:disabled {
    @apply opacity-50 cursor-not-allowed;
    transform: none !important;
}

.dark .discarded-tile {
    @apply text-gray-200;
}

.dark .tile {
    @apply bg-gray-100 text-gray-900;
}

.dark .tile-back {
    @apply bg-gray-600;
}
</style>
@endsection