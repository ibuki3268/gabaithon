<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\HomeController;


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

// 認証ルート
require __DIR__.'/auth.php';