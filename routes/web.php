<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Home;
use App\Livewire\TelegramLinks;
use App\Models\Tmp;
use App\Models\Tmp2;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use App\Jobs\ProcessPodcast;
use App\Models\User;
use Illuminate\Support\Facades\DB;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', Home::class);

Route::get('/tmp', function () {
    return User::query()->create([
        'name' => fake()->name(),
        'email' => fake()->unique()->safeEmail(),
        'email_verified_at' => now(),
        'password' => '222',
    ]);
});

Route::get('/telegram-links', TelegramLinks::class)->name('telegram-links');