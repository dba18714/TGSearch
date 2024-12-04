<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ChatController;
use App\Telegram\Handlers\StartHandler;
use SergiX44\Nutgram\Nutgram;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/tmp', function () {
    return 123;
});

Route::post('telegram/webhook', function (Nutgram $bot) {
    // \Log::debug('Webhook request received', [
    //     'raw_content' => file_get_contents('php://input'),
    //     'content_type' => request()->header('Content-Type')
    // ]);

    $bot->run();
});

Route::any('telegram/webhook2', function (Nutgram $bot) {
    echo 'hi';
    // \Log::debug('Webhook request received', [
    //     'raw_content' => file_get_contents('php://input'),
    //     'content_type' => request()->header('Content-Type')
    // ]);

    
});
