<?php

use App\Http\Controllers\LuckyPageController;
use Illuminate\Support\Facades\Route;

Route::get('/lucky/{token}', [LuckyPageController::class, 'index'])
    ->name('lucky.index');

Route::post('/lucky/{token}/generate-link', [LuckyPageController::class, 'regenerateLink'])
    ->name('lucky.generate-link');

Route::post('/lucky/{token}/deactivate-link', [LuckyPageController::class, 'deactivateLink'])
    ->name('lucky.deactivate-link');

Route::post('/lucky/{token}/play', [LuckyPageController::class, 'playLuckyGame'])
    ->name('lucky.play');

Route::post('/lucky/{token}/history', [LuckyPageController::class, 'getHistory'])
    ->name('lucky.history');

require __DIR__.'/auth.php';
