<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Home;
use App\Livewire\Links;
use App\Livewire\LinkShow;
use App\Models\Tmp;
use App\Models\Tmp2;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use App\Jobs\ProcessPodcast;
use App\Jobs\UpdateLinkInfoJob;
use App\Models\Link;
use App\Models\User;
use Illuminate\Support\Facades\DB;

Route::get('/tmp', function () {
    $link = Link::find('01jcdr9bqrzfvxs08awhjy17jv');
    if($link) $link->dispatchUpdateJob(); 
    dump($link);
    
});

Route::get('/tmp2', function () {
    dump('tmp2 start: ' . now());
    dump('tmp2 end: ' . now());
    return 'tmp2';
});

Route::get('/', Links::class)->name('home');
Route::get('/links/{link}', LinkShow::class)->name('link.show');