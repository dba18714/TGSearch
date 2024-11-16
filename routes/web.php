<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Home;
use App\Livewire\Owners;
use App\Livewire\OwnerShow;
use App\Livewire\OwnerCreate;
use App\Models\Tmp;
use App\Models\Tmp2;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use App\Jobs\ProcessPodcast;
use App\Jobs\ProcessUpdateOwnerInfoJob;
use App\Models\Message;
use App\Models\Owner;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

Route::get('/home', Home::class);
Route::view('/welcome', 'welcome');

Route::get('/tmp', function () {
    if (preg_match('/^@\w+$/', '@Nonnie25682')) {
        return 'person';
    }

    return 'not person';
    $owner = Owner::find('01jcq1y39hh0rbc7nxykrhayyj');
    dump($owner);
    dump($owner->photo_count);
    return $owner->photo_count;
    $message = Message::firstOrCreate(
        ['owner_id' => '01jcnh2hfyzcqt380f6apz3b3k', 'original_id' => '874791'],
        [
            'source' => 'manual',
        ]
    );
    dd($message);
    return $message;
    
    function tmp(Model $model) {
        return class_basename($model);
    };
    
    return tmp(Owner::first());



    return Owner::create([
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




    $owner = Owner::create([
        'url' => 'https://www.youtube.com/watch?v=1234567890',
    ]);
    return $owner;
    if ($owner) $owner->dispatchUpdateJob();
    dump($owner);
});

Route::get('/tmp2', function () {
    dump('tmp2 start: ' . now());
    dump('tmp2 end: ' . now());
    return 'tmp2';
});

Route::get('/', Owners::class)->name('home');
Route::get('/owners/create', OwnerCreate::class)->name('owners.create');
Route::get('/owners/{owner}/{message?}', OwnerShow::class)->name('owner.show');

Route::get('/owner/{id}/{message_id?}', function (string $id = null, ?string $message_id = null) {
    return $id . '-' . $message_id;
});
