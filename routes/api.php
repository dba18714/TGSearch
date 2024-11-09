<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TelegramLinkController;

// 公开接口
Route::get('/telegram-links', [TelegramLinkController::class, 'index']);
Route::get('/telegram-links/{telegramLink}', [TelegramLinkController::class, 'show']);

// 需要认证的接口
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/telegram-links', [TelegramLinkController::class, 'store']);
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/tmp', function () {
    return 123;
});
