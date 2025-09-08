<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\GachaController;
use App\Http\Controllers\FriendController;


Route::get('/test', function () {
    return 'Sailテスト成功！Laravel動作中！';
});

// 認証が必要なルート
Route::middleware('auth')->group(function () {
    Route::get('/', [HomeController::class, 'firstshow'])->name('dashboard');
    Route::post('/selectcourse', [HomeController::class, 'selectcourse'])->name('selectcourse');
    Route::post('/selectdifficulty', [HomeController::class, 'selectdifficulty'])->name('selectdifficulty');
  
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
    // フレンド一覧画面
    Route::get('/friends', [FriendController::class, 'index'])->name('friends');
    
    // フレンド検索
    Route::get('/friends/search', [FriendController::class, 'search'])->name('friends.search');
    
    // フレンド申請送信
    Route::post('/friends/request/{user}', [FriendController::class, 'sendRequest'])->name('friends.request');
    
    // フレンド申請承認
    Route::post('/friends/accept/{request}', [FriendController::class, 'acceptRequest'])->name('friends.accept');
    
    // フレンド申請拒否
    Route::post('/friends/reject/{request}', [FriendController::class, 'rejectRequest'])->name('friends.reject');
    
    // フレンド削除
    Route::delete('/friends/{friend}', [FriendController::class, 'removeFriend'])->name('friends.remove');
});

// 認証ルート
require __DIR__.'/auth.php';