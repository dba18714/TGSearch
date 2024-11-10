<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LinkController;

// 公开接口
Route::get('/links', [LinkController::class, 'index']);
Route::get('/links/{link}', [LinkController::class, 'show']);

// 需要认证的接口
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/links', [LinkController::class, 'store']);
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/tmp', function () {
    return 123;
});
