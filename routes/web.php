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
use App\Livewire\Search;
use App\Models\Message;
use App\Models\Chat;
use App\Models\Impression;
use App\Models\UnifiedSearch;
use App\Models\User;
use App\Services\UnifiedSearchService;
use App\Settings\GeneralSettings;
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
    echo now()->timezone('Asia/Taipei')->subMinutes(60);
    dump(opcache_get_status()); // TODO 用filament管理
    dump(opcache_get_configuration());
    return;

    $settings = app(GeneralSettings::class);

    $level1_amount = $settings->level1_commission_amount;
    $level2_amount = $settings->level2_commission_amount;
    dump($level1_amount);
    dump($level2_amount);


    return view('welcome');
    // 1. 关闭已存在的所有缓冲区
    while (ob_get_level()) {
        ob_end_clean();
    }

    // 2. 开启新的输出缓冲区
    ob_start();

    // 3. 设置必要的 header
    header('Content-Type: text/html; charset=utf-8');
    header('X-Accel-Buffering: no'); // 禁用 Nginx 的输出缓冲
    header('Cache-Control: no-cache');

    // 4. 关闭 FastCGI 的缓冲
    if (function_exists('apache_setenv')) {
        apache_setenv('no-gzip', 1);
    }

    // 5. 输出内容
    for ($i = 0; $i < 30; $i++) {
        echo 'hello' . $i . '<br>';
        echo str_pad('', 8192); // 填充输出缓冲区到一定大小以强制输出
        ob_flush();  // 刷新 PHP 输出缓冲区
        flush();     // 将数据发送到客户端
        sleep(1);
    }

    // 6. 清理并关闭输出缓冲区
    ob_end_flush();

    return 'done';
});

Route::get('/tmp2', function () {});

Route::get('/', Search::class)->name('home');
Route::get('/chats/create', ChatCreate::class)->name('chats.create');
Route::get('/chats/{chat}/{message?}', ChatShow::class)->name('chat.show');

Route::get('/chat/{id}/{message_id?}', function (string $id = null, ?string $message_id = null) {
    return $id . '-' . $message_id;
});
