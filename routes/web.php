<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\GachaController;


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
    // ガチャ画面表示
    Route::get('/gacha', [GachaController::class, 'index'])->name('gacha');
    
    // ガチャ実行
    Route::post('/gacha/draw', [GachaController::class, 'draw'])->name('gacha.draw');
    
    // ガチャ結果表示
    Route::get('/gacha/result', [GachaController::class, 'result'])->name('gacha.result');
});

// 認証ルート
require __DIR__.'/auth.php';