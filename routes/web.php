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

// ゲームルート（認証必要）
Route::middleware('auth')->group(function () {
    // 学習モード
    Route::get('/game', [GameController::class, 'index'])->name('game');
    
    // 対戦モード
    Route::prefix('vs')->name('vs.')->group(function () {
        // 対戦画面表示
        Route::get('/battle', [VsController::class, 'battle'])->name('battle');
        
        // ツモして捨てるアクション
        Route::post('/draw-and-discard/{tileIndex}', [VsController::class, 'drawAndDiscard'])
            ->name('drawAndDiscard');
        
        // ロン判定
        Route::post('/check-ron/{discardingPlayer}/{tileIndex}', [VsController::class, 'checkRon'])
            ->name('checkRon');
            
        // リーチ宣言
        Route::post('/declare-riichi/{player}', [VsController::class, 'declareRiichi'])
            ->name('declareRiichi');
        
        // ゲームリセット
        Route::get('/reset', [VsController::class, 'reset'])->name('reset');
        
        // ゲーム統計（API）
        Route::get('/stats', [VsController::class, 'stats'])->name('stats');
    });
});

// 認証ルート
require __DIR__.'/auth.php';