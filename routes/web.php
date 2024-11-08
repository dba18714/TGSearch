<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Home;
use App\Models\Plan;
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



    User::query()->create(['email' => now(), 'password' => now()]);
    return User::query()->get();

    return Carbon::now()->toDateTimeString();

    config()->set('app.timezone', 'Asia/Shanghai');

    return (Tmp::query()->create(['name' => time()])->created_at->toString());

    // 创建一个新的 Tmp 模型实例
    $tmp = Tmp::create(['name' => time()]);

    // 检查数据库中存储的时间（直接从数据库查询）
    $dbTime = DB::select("SELECT created_at FROM tmps ORDER BY id DESC LIMIT 1");

    return response()->json([
        'stored_created_at' => $tmp->created_at,  // Laravel 读取的时间
        'db_created_at' => $dbTime[0]->created_at  // 直接从数据库查询的时间
    ]);

    // $result = DB::select("SELECT created_at FROM tmps ORDER BY id DESC LIMIT 1");

    // return response()->json($result);

    // // 检查 PHP 默认时区
    // $phpTimezone = date_default_timezone_get();

    // // 检查 Laravel 应用的时区
    // $appTimezone = config('app.timezone');

    // return response()->json([
    //     'php_timezone' => $phpTimezone,
    //     'app_timezone' => $appTimezone,
    // ]);

    // // 返回：{"php_timezone":"Asia\/Shanghai","app_timezone":"Asia\/Shanghai"}

    // $timezone = DB::select("SHOW TIMEZONE;");
    // return $timezone;

    // 返回：[{"TimeZone":"+00:00"}]

    return (Tmp::query()->create(['name' => time()]));

    // echo date("Y-m-d H:i:s T");
    // echo '11';

    return (Tmp::query()->create(['name' => time()]));

    return (Tmp::query()->create(['name' => time()])->created_at->toString());

    // Carbon::setTimezone('Asia/Shanghai');

    // Carbon::setLocale('Asia/Shanghai');
    // config(['app.timezone' => 'Asia/Shanghai']);

    // return config('app.timezone');
    // return Carbon::getLocale();

    // Carbon::setTimezone('Asia/Shanghai');
    // date_default_timezone_set('Asia/Shanghai');

    // ProcessPodcast::dispatchSync();
    // ProcessPodcast::dispatch();
    // ProcessPodcast::dispatch()->delay(now()->addMinutes(10));

    // return;

    // config(['app.timezone' => 'Asia/Shanghai']);
    // return(Tmp2::whereDate('created_at', Carbon::today())->first());

    $startOfDay = Carbon::today()->timestamp;
    $endOfDay = Carbon::tomorrow()->timestamp - 1;
    return Tmp2::whereBetween('created_at', [$startOfDay, $endOfDay])->first();

    // return(Tmp::query()->orderByDesc('id')->first()->created_at->toString());
    // return(Tmp::query()->create(['name' => time()])->created_at->toString());
    // return(Tmp2::query()->create(['id' => time()])->created_at->toString());



    return (Plan::where('created_at', '2024-10-10 04:52:30+08:00')->first());


    dump(Plan::query()->create(['title' => time(), 'description' => time()]));
    return (Plan::query()->orderByDesc('id')->first());

    dump(Tmp::query()->create(['name' => time()]));
    return (Tmp::query()->orderByDesc('id')->first());

    dump(Tmp::where('created_at', '2024-10-10 02:10:00+08:00')->first()->created_at->setTimezone('Asia/Shanghai'));

    $flight = new Tmp;

    $flight->name = time();


    dump($flight->save());

    dump(Tmp::query()->create(['name' => time()]));

    dump(Tmp::query()->orderByDesc('id')->first());


    return;




    return Plan::query()->get();
    return (123);
});
