<?php

use App\Http\Controllers\DiscardedTileController;
use Illuminate\Support\Facades\Route;

// 捨て牌機能のルート
Route::middleware('auth')->group(function () {
    // 捨て牌一覧画面
    Route::get('/discarded-tiles', [DiscardedTileController::class, 'index'])
        ->name('discarded.tiles');
    
    // 捨て牌から再挑戦（既存のクイズルートを利用）
    Route::post('/discarded-tiles/{tile}/retry', [DiscardedTileController::class, 'retry'])
        ->name('discarded.retry');
});