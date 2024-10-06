<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Home;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', Home::class);
