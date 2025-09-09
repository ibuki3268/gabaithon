<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// 麻雀ゲーム用パブリックチャンネル
Broadcast::channel('mahjong-game-{gameId}', function ($user, $gameId) {
    // パブリックチャンネルとして設定（認証不要）
    return true;
});

// プライベートゲームルーム用（将来的に認証が必要な場合）
Broadcast::channel('private-mahjong-game-{gameId}', function ($user, $gameId) {
    // ユーザーがそのゲームに参加する権限があるかチェック
    // 例：ゲームのプレイヤーリストに含まれているか
    return $user && in_array($user->id, $this->getGamePlayers($gameId));
});