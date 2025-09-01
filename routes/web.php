<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\QuizController; // QuizControllerを使う宣言を追加

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

// ログイン後のダッシュボード（ホーム画面）
Route::get('/dashboard', [HomeController::class, 'index'])
    ->middleware(['auth', 'verified'])->name('dashboard');

// クイズ画面表示用の新しいルートを追加
// {tile} の部分には、挑戦する牌のIDが入ります (例: /quiz/1)
Route::get('/quiz/{tile}', [QuizController::class, 'show'])
    ->middleware(['auth', 'verified'])->name('quiz.show');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
