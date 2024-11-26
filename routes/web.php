<?php

use App\ContentAudit\Facades\ContentAudit;
use Illuminate\Support\Facades\Route;
use App\Livewire\Home;
use App\Livewire\Entities;
use App\Livewire\EntityShow;
use App\Livewire\EntityCreate;
use App\Models\Tmp;
use App\Models\Tmp2;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use App\Jobs\ProcessPodcast;
use App\Jobs\ProcessUpdateEntityInfoJob;
use App\Models\Message;
use App\Models\Entity;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
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

Route::get('/tmp', function () {

    $data = now()->addDay()->toString();
    dump($data);
    $data = now()->addDays(1)->toString();
    dump($data);
    $data = now()->addDays(2)->toString();
    dump($data);
    $data = now()->addDays(3)->toString();
    dump($data);
    $data = now()->toString();
    dump($data);


    return $data;

    $result = ContentAudit::driver('openai')
                ->audit('rgdsfd');

                return $result;
    if (preg_match('/^@\w+$/', '@Nonnie25682')) {
        return 'person';
    }

    return 'not person';
    $entity = Entity::find('01jcq1y39hh0rbc7nxykrhayyj');
    dump($entity);
    dump($entity->photo_count);
    return $entity->photo_count;
    $message = Message::firstOrCreate(
        ['entity_id' => '01jcnh2hfyzcqt380f6apz3b3k', 'original_id' => '874791'],
        [
            'source' => 'manual',
        ]
    );
    dd($message);
    return $message;
    
    function tmp(Model $model) {
        return class_basename($model);
    };
    
    return tmp(Entity::first());



    return Entity::create([
        'username' => '@test'.time(),
    ])->url;
    return;


    $url1 = 'https://t.me/jichang_list/3675';
    $url2 = 'https://t.me/s/jichang_list?before=3676';
    $url3 = 'https://t.me/s/jichang_list?q=%23%E5%B9%B2%E5%90%A7%E6%9C%BA%E5%9C%BA&before=3676';
    $url4 = 'https://t.me/jichang_list';


    echo '1-' . extract_telegram_message_id_by_url($url1) . '<br>'; // 输出: 3675
    echo '2-' . extract_telegram_message_id_by_url($url2) . '<br>'; // 输出: 3676
    echo '3-' . extract_telegram_message_id_by_url($url3) . '<br>'; // 输出: 3676
    echo '4-' . extract_telegram_message_id_by_url($url4) . '<br>'; // 输出: 3676


    echo '1-' . extract_telegram_username_by_url($url1) . '<br>'; // 输出: jichang_list
    echo '2-' . extract_telegram_username_by_url($url2) . '<br>'; // 输出: jichang_list
    echo '3-' . extract_telegram_username_by_url($url3) . '<br>'; // 输出: jichang_list
    echo '4-' . extract_telegram_username_by_url($url4) . '<br>'; // 输出: jichang_list

    return;




    $entity = Entity::create([
        'url' => 'https://www.youtube.com/watch?v=1234567890',
    ]);
    return $entity;
    if ($entity) $entity->dispatchUpdateJob();
    dump($entity);
});

Route::get('/tmp2', function () {
    dump('tmp2 start: ' . now());
    dump('tmp2 end: ' . now());
    return 'tmp2';
});

Route::get('/', Entities::class)->name('home');
Route::get('/entities/create', EntityCreate::class)->name('entities.create');
Route::get('/entities/{entity}/{message?}', EntityShow::class)->name('entity.show');

Route::get('/entity/{id}/{message_id?}', function (string $id = null, ?string $message_id = null) {
    return $id . '-' . $message_id;
});
