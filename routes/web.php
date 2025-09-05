<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\QuizController; // QuizController���g���錾��ǉ�

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Route::get('/', function () {
//     return view('welcome');
// });

// ���O�C����̃_�b�V���{�[�h�i�z�[����ʁj
Route::get('/dashboard', [HomeController::class, 'index'])
    ->middleware(['auth', 'verified'])->name('dashboard');

// �N�C�Y��ʕ\���p�̐V�������[�g��ǉ�
// {tile} �̕����ɂ́A���킷��v��ID������܂� (��: /quiz/1)
Route::get('/quiz/{tile}', [QuizController::class, 'show'])
    ->middleware(['auth', 'verified'])->name('quiz.show');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
