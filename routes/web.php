<?php

use App\ContentAudit\Facades\ContentAudit;
use Illuminate\Support\Facades\Route;
use App\Livewire\Home;
use App\Livewire\Chats;
use App\Livewire\ChatShow;
use App\Livewire\ChatCreate;
use App\Models\Tmp;
use App\Models\Tmp2;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use App\Jobs\ProcessPodcast;
use App\Jobs\ProcessUpdateChatInfoJob;
use App\Models\Message;
use App\Models\Chat;
use App\Models\UnifiedSearch;
use App\Models\User;
use App\Services\SearchService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

Route::get('/robots.txt', function () {
    $content = [
        'User-agent: *',
        'Disallow: /api/*',
        '',
        'Sitemap: ' . url('sitemap.xml'),
    ];

    return response(implode(PHP_EOL, $content))
        ->header('Content-Type', 'text/plain');
});

Route::get('/home', Home::class);
Route::view('/welcome', 'welcome');

Route::get('/tmp', function (Request $request) {
    Chat::query()->create([
        'username' => time(),
        'is_valid' => true,
    ]);
    Message::query()->create([
        'original_id' => time(),
        'chat_id' => time(),
        'is_valid' => true,
    ]);
    UnifiedSearch::query()->create([
        'content' => 333,
        'searchable_type' => 'App\Models\User',
        'searchable_id' => 1,
    ]);
    $result = app(SearchService::class)->search($request->query('q') ?? 'ex');
    dump($result);
    return $result;
});

Route::get('/tmp2', function () {
    dump('tmp2 start: ' . now());
    dump('tmp2 end: ' . now());
    return 'tmp2';
});

Route::get('/', Chats::class)->name('home');
Route::get('/chats/create', ChatCreate::class)->name('chats.create');
Route::get('/chats/{chat}/{message?}', ChatShow::class)->name('chat.show');

Route::get('/chat/{id}/{message_id?}', function (string $id = null, ?string $message_id = null) {
    return $id . '-' . $message_id;
});
