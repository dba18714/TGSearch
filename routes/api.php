<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ChatController;
use SergiX44\Nutgram\Nutgram;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/tmp', function () {
    return 123;
});

Route::post('telegram/webhook', function (Nutgram $bot) {
    $bot->run();
});
