<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\GachaController;
use App\Http\Controllers\FriendController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\VsController;

Route::get('/test', function () {
    return 'Sailテスト成功！Laravel動作中！';
});

// 認証が必要なルート
Route::middleware('auth')->group(function () {
    Route::get('/', [HomeController::class, 'firstshow'])->name('dashboard');
    Route::post('/selectcourse', [HomeController::class, 'selectcourse'])->name('selectcourse');
    Route::post('/selectdifficulty', [HomeController::class, 'selectdifficulty'])->name('selectdifficulty');
    Route::post('/selectyaku', [HomeController::class, 'selectyaku'])->name('selectyaku');
    Route::post('/selecthai', [HomeController::class, 'selecthai'])->name('selecthai');
    Route::post('/newmakedata', [HomeController::class, 'newmakedata'])->name('newmakedata');
  
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// クイズルート
Route::middleware('auth')->prefix('quiz')->name('quiz.')->group(function () {
    Route::get('/start', [QuizController::class, 'start'])->name('start');
    Route::get('/start/course/{course}/difficulty/{difficulty}/yaku/{yaku}', [QuizController::class, 'start'])->name('start.with.params');
    Route::post('/answer', [QuizController::class, 'answer'])->name('answer');
    Route::get('/result', [QuizController::class, 'result'])->name('result');
    Route::get('/{tile}', [QuizController::class, 'show'])->name('show');
});

// ガチャルート
Route::middleware('auth')->group(function () {
    Route::get('/gacha', [GachaController::class, 'index'])->name('gacha');
    Route::post('/gacha/draw', [GachaController::class, 'draw'])->name('gacha.draw');
    Route::get('/gacha/result', [GachaController::class, 'result'])->name('gacha.result');
});

// フレンドルート
Route::middleware('auth')->group(function () {
    Route::get('/friends', [FriendController::class, 'index'])->name('friends');
    Route::get('/friends/search', [FriendController::class, 'search'])->name('friends.search');
    Route::post('/friends/request/{user}', [FriendController::class, 'sendRequest'])->name('friends.request');
    Route::post('/friends/accept/{request}', [FriendController::class, 'acceptRequest'])->name('friends.accept');
    Route::post('/friends/reject/{request}', [FriendController::class, 'rejectRequest'])->name('friends.reject');
    Route::delete('/friends/{friend}', [FriendController::class, 'removeFriend'])->name('friends.remove');
});

// 麻雀ゲームルート
Route::middleware('auth')->prefix('vs')->name('vs.')->group(function () {
    // メイン対戦画面
    Route::get('/battle', [VsController::class, 'battle'])->name('battle');
    
    // ゲーム操作
    Route::get('/draw-discard/{tileIndex}', [VsController::class, 'drawAndDiscard'])->name('draw-discard');
    Route::get('/riichi/{player}', [VsController::class, 'declareRiichi'])->name('riichi');
    Route::get('/ron/{discardingPlayer}/{tileIndex}', [VsController::class, 'checkRon'])->name('ron');
    
    // ゲーム管理
    Route::get('/reset', [VsController::class, 'reset'])->name('reset');
    Route::get('/stats', [VsController::class, 'stats'])->name('stats');
    
    // プレイヤー参加・退出（将来の拡張用）
    Route::post('/join/{playerId}', [VsController::class, 'joinGame'])->name('join');
    Route::post('/leave/{playerId}', [VsController::class, 'leaveGame'])->name('leave');
    
    // ゲーム状態取得（AJAX用）
    Route::get('/state', [VsController::class, 'getGameState'])->name('state');
});

// 認証ルート
require __DIR__.'/auth.php';