<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ChatController;
use SergiX44\Nutgram\Nutgram;

// 公开接口
Route::get('/chats', [ChatController::class, 'index']);
Route::get('/chats/{link}', [ChatController::class, 'show']);

// 需要认证的接口
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/chats', [ChatController::class, 'store']);
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/tmp', function () {
    return 123;
});

Route::post('telegram/webhook', function (Nutgram $bot) {
    $bot->run();
});
