<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\OwnerController;
use SergiX44\Nutgram\Nutgram;

// 公开接口
Route::get('/owners', [OwnerController::class, 'index']);
Route::get('/owners/{link}', [OwnerController::class, 'show']);

// 需要认证的接口
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/owners', [OwnerController::class, 'store']);
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
