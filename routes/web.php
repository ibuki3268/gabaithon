<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\HomeController;

// ��{���[�g
Route::get('/', function () {
    return view('welcome');
});

Route::get('/test', function () {
    return 'Sail�e�X�g�����ILaravel���쒆�I';
});

// �F�؂��K�v�ȃ��[�g
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');
    
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// �N�C�Y���[�g
Route::middleware('auth')->prefix('quiz')->name('quiz.')->group(function () {
    Route::get('/start', [QuizController::class, 'start'])->name('start');
    Route::get('/start/course/{course}/difficulty/{difficulty}/yaku/{yaku}', [QuizController::class, 'start'])->name('start.with.params');
    Route::post('/answer', [QuizController::class, 'answer'])->name('answer');
    Route::get('/result', [QuizController::class, 'result'])->name('result');
    Route::get('/{tile}', [QuizController::class, 'show'])->name('show');
});

// �F�؃��[�g
require __DIR__.'/auth.php';